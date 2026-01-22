<?php
$pageTitle = 'Register';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /dashboard');
    exit;
}

$preselectedRole = $_GET['role'] ?? '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'email' => sanitize($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'role' => sanitize($_POST['role'] ?? ''),
        'first_name' => sanitize($_POST['first_name'] ?? ''),
        'last_name' => sanitize($_POST['last_name'] ?? ''),
        'phone' => sanitize($_POST['phone'] ?? ''),
        'business_name' => sanitize($_POST['business_name'] ?? ''),
        'license_number' => sanitize($_POST['license_number'] ?? ''),
        'specialty' => sanitize($_POST['specialty'] ?? ''),
    ];
    
    $errors = [];
    
    if (empty($data['email']) || !validateEmail($data['email'])) {
        $errors[] = 'Valid email is required';
    }
    if (empty($data['password']) || strlen($data['password']) < 8) {
        $errors[] = 'Password must be at least 8 characters';
    }
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $errors[] = 'Passwords do not match';
    }
    if (empty($data['role']) || !in_array($data['role'], ['doctor', 'dentist', 'pharmacy', 'accountant'])) {
        $errors[] = 'Please select a valid role';
    }
    if (empty($data['first_name']) || empty($data['last_name'])) {
        $errors[] = 'First and last name are required';
    }
    
    if (empty($errors)) {
        $result = registerUser($data);
        if ($result['success']) {
            // Auto login after registration
            loginUser($data['email'], $data['password']);
            setFlashMessage('success', 'Welcome to MediTax Connect! Your account has been created.');
            header('Location: /dashboard');
            exit;
        } else {
            setFlashMessage('error', $result['error']);
        }
    } else {
        setFlashMessage('error', implode('. ', $errors));
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
?>

<div class="min-h-[80vh] py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Create Your Account</h1>
                <p class="text-gray-600 mt-2">Join MediTax Connect today</p>
            </div>
            
            <form method="POST" action="/register" class="space-y-6" id="registerForm">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <!-- Role Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">I am a...</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <label class="relative">
                            <input type="radio" name="role" value="doctor" class="peer sr-only" <?php echo $preselectedRole === 'healthcare' ? 'checked' : ''; ?> required>
                            <div class="border-2 rounded-lg p-4 text-center cursor-pointer peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:border-gray-300 transition">
                                <i class="fas fa-stethoscope text-2xl text-primary-600 mb-2"></i>
                                <div class="font-medium text-sm">Doctor</div>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="role" value="dentist" class="peer sr-only">
                            <div class="border-2 rounded-lg p-4 text-center cursor-pointer peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:border-gray-300 transition">
                                <i class="fas fa-tooth text-2xl text-primary-600 mb-2"></i>
                                <div class="font-medium text-sm">Dentist</div>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="role" value="pharmacy" class="peer sr-only">
                            <div class="border-2 rounded-lg p-4 text-center cursor-pointer peer-checked:border-primary-500 peer-checked:bg-primary-50 hover:border-gray-300 transition">
                                <i class="fas fa-prescription-bottle-medical text-2xl text-primary-600 mb-2"></i>
                                <div class="font-medium text-sm">Pharmacy</div>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="role" value="accountant" class="peer sr-only" <?php echo $preselectedRole === 'accountant' ? 'checked' : ''; ?>>
                            <div class="border-2 rounded-lg p-4 text-center cursor-pointer peer-checked:border-accent-500 peer-checked:bg-accent-50 hover:border-gray-300 transition">
                                <i class="fas fa-calculator text-2xl text-accent-600 mb-2"></i>
                                <div class="font-medium text-sm">Accountant</div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- Personal Info -->
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                               placeholder="John">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                               placeholder="Smith">
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                           placeholder="you@example.com">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" id="phone" name="phone" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                           placeholder="+1 (555) 123-4567">
                </div>
                
                <!-- Business Info -->
                <div id="businessInfo" class="space-y-4">
                    <div>
                        <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">Business/Practice Name</label>
                        <input type="text" id="business_name" name="business_name" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                               placeholder="ABC Medical Clinic">
                    </div>
                    <div id="licenseField">
                        <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">License Number</label>
                        <input type="text" id="license_number" name="license_number" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                               placeholder="MD-12345">
                    </div>
                    <div id="specialtyField">
                        <label for="specialty" class="block text-sm font-medium text-gray-700 mb-2">Specialty</label>
                        <input type="text" id="specialty" name="specialty" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                               placeholder="e.g., General Practice, Cardiology">
                    </div>
                </div>
                
                <!-- Password -->
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" required minlength="8"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                               placeholder="Min 8 characters">
                    </div>
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                               placeholder="Repeat password">
                    </div>
                </div>
                
                <div class="flex items-start">
                    <input type="checkbox" id="terms" required class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="terms" class="ml-2 text-sm text-gray-600">
                        I agree to the <a href="#" class="text-primary-600 hover:underline">Terms of Service</a> and 
                        <a href="#" class="text-primary-600 hover:underline">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" class="w-full gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition">
                    Create Account
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Already have an account? 
                    <a href="/login" class="text-primary-600 font-semibold hover:text-primary-700">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Update form fields based on selected role
document.querySelectorAll('input[name="role"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const licenseField = document.getElementById('licenseField');
        const specialtyField = document.getElementById('specialtyField');
        
        if (this.value === 'accountant') {
            document.getElementById('license_number').placeholder = 'CPA-12345';
            document.getElementById('specialty').placeholder = 'e.g., Tax Accounting, Healthcare Finance';
        } else if (this.value === 'pharmacy') {
            document.getElementById('license_number').placeholder = 'PHARM-12345';
            specialtyField.style.display = 'none';
        } else {
            document.getElementById('license_number').placeholder = 'MD-12345';
            document.getElementById('specialty').placeholder = 'e.g., General Practice, Cardiology';
            specialtyField.style.display = 'block';
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
