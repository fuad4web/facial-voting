<x-guest-layout>

            <!-- Facial Login Form -->
            <div id="facialLoginForm">
                <!-- Camera Feed -->
                <div class="mb-6">
                    <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="height: 300px;">
                        <video id="video" width="100%" height="100%" autoplay muted class="object-cover"></video>
                        <canvas id="canvas" class="absolute top-0 left-0 w-full h-full"></canvas>
                    </div>

                    <!-- Status Messages -->
                    <div id="faceStatus" class="mt-3 text-sm text-center text-gray-600"></div>
                    
                    <!-- Progress Bar -->
                    <div id="progressContainer" class="mt-3 hidden">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div id="progressBar" class="bg-indigo-600 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                        <p id="progressText" class="text-xs text-center mt-1 text-gray-600">Processing...</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col space-y-3">
                    <button type="button" id="startLoginBtn" 
                            class="w-full bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        Start Facial Login
                    </button>

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Or</span>
                        </div>
                    </div>

                    <a href="{{ route('login') }}" 
                       class="w-full bg-gray-200 text-gray-700 py-3 px-4 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-center transition-colors">
                        Use Email & Password
                    </a>

                    <p class="text-center text-sm text-gray-600 mt-4">
                        Don't have an account?
                        <a href="{{ route('facial.register') }}" class="text-indigo-600 hover:text-indigo-900">
                            Register here
                        </a>
                    </p>
                </div>
            </div>

            <!-- Hidden input for facial descriptors -->
            <input type="hidden" id="facial_descriptors" name="facial_descriptors">
        </div>
    </div>

</x-guest-layout>

    <!-- Face API Script -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const startLoginBtn = document.getElementById('startLoginBtn');
        const faceStatus = document.getElementById('faceStatus');
        const progressContainer = document.getElementById('progressContainer');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const facialDescriptors = document.getElementById('facial_descriptors');

        let faceDetected = false;
        let detectionInterval = null;
        let modelsLoaded = false;
        let loginAttempts = 0;
        const MAX_ATTEMPTS = 3;

        // Load face-api models
        async function loadModels() {
            faceStatus.textContent = 'Loading face detection models...';
            faceStatus.classList.remove('text-red-500', 'text-green-600');
            faceStatus.classList.add('text-gray-600');
            
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('/models')
                ]);
                
                modelsLoaded = true;
                faceStatus.textContent = 'Models loaded. Click "Start Facial Login" to begin.';
                faceStatus.classList.add('text-green-600');
            } catch (error) {
                console.error('Error loading models:', error);
                faceStatus.textContent = 'Error loading face detection models. Please refresh.';
                faceStatus.classList.add('text-red-500');
            }
        }

        // Start video stream
        async function startVideo() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: 640, 
                        height: 480,
                        facingMode: 'user'
                    } 
                });
                video.srcObject = stream;
            } catch (err) {
                faceStatus.textContent = 'Error accessing camera: ' + err.message;
                faceStatus.classList.add('text-red-500');
                startLoginBtn.disabled = true;
            }
        }

        // Start face detection
        function startFaceDetection() {
            if (!modelsLoaded) {
                alert('Models still loading. Please wait.');
                return;
            }

            loginAttempts = 0;
            faceStatus.textContent = 'Looking for face...';
            
            const displaySize = { width: video.width, height: video.height };
            faceapi.matchDimensions(canvas, displaySize);

            detectionInterval = setInterval(async () => {
                const detections = await faceapi.detectAllFaces(video, 
                    new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
                
                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                
                // Clear canvas
                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                
                if (detections.length === 1) {
                    faceDetected = true;
                    
                    // Draw face detection box
                    faceapi.draw.drawDetections(canvas, resizedDetections);
                    faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
                    
                    faceStatus.textContent = 'Face detected! Verifying...';
                    faceStatus.classList.remove('text-red-500');
                    faceStatus.classList.add('text-green-600');
                    
                    // Auto-login when face is detected
                    performFacialLogin(detections[0].descriptor);
                    
                    // Clear interval after detection
                    clearInterval(detectionInterval);
                } else if (detections.length > 1) {
                    faceDetected = false;
                    faceStatus.textContent = 'Multiple faces detected. Please ensure only one face is visible.';
                    faceStatus.classList.add('text-red-500');
                } else {
                    faceDetected = false;
                    faceStatus.textContent = 'No face detected. Please position your face in the camera.';
                    faceStatus.classList.add('text-red-500');
                }
            }, 100);
        }

        // Perform facial login
        async function performFacialLogin(descriptor) {
            loginAttempts++;
            
            // Show progress
            progressContainer.classList.remove('hidden');
            progressBar.style.width = '50%';
            progressText.textContent = 'Verifying identity...';

            // Convert descriptor to array and store
            const descriptorsArray = Array.from(descriptor);
            facialDescriptors.value = JSON.stringify(descriptorsArray);

            try {
                // Send login request
                const response = await fetch('{{ route("facial.login.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        facial_descriptors: facialDescriptors.value
                    })
                });

                const data = await response.json();

                if (data.success) {
                    progressBar.style.width = '100%';
                    progressText.textContent = 'Login successful! Redirecting...';
                    
                    // Stop video
                    if (video.srcObject) {
                        video.srcObject.getTracks().forEach(track => track.stop());
                    }
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Successful!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    progressBar.style.width = '0%';
                    progressContainer.classList.add('hidden');
                    
                    if (loginAttempts < MAX_ATTEMPTS) {
                        faceStatus.textContent = `Face not recognized. Attempt ${loginAttempts} of ${MAX_ATTEMPTS}`;
                        faceStatus.classList.add('text-red-500');
                        
                        // Restart detection
                        setTimeout(() => {
                            startFaceDetection();
                        }, 2000);
                    } else {
                        faceStatus.textContent = 'Maximum attempts reached. Please use email login.';
                        faceStatus.classList.add('text-red-500');
                        
                        // Stop video
                        if (video.srcObject) {
                            video.srcObject.getTracks().forEach(track => track.stop());
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed',
                            text: 'Maximum login attempts reached. Please use email and password.',
                            confirmButtonText: 'Go to Email Login'
                        }).then(() => {
                            window.location.href = '{{ route("login") }}';
                        });
                    }
                }
            } catch (error) {
                console.error('Login error:', error);
                faceStatus.textContent = 'Error during login. Please try again.';
                faceStatus.classList.add('text-red-500');
                progressContainer.classList.add('hidden');
            }
        }

        // Event listeners
        startLoginBtn.addEventListener('click', () => {
            startLoginBtn.disabled = true;
            startLoginBtn.classList.add('opacity-50', 'cursor-not-allowed');
            startFaceDetection();
        });

        // Initialize
        loadModels();
        startVideo();

        // Clean up on page unload
        window.addEventListener('beforeunload', () => {
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }
            if (detectionInterval) {
                clearInterval(detectionInterval);
            }
        });
    </script>
