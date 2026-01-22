<?php
$pageTitle = 'Home';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
?>

<!-- Hero Section -->
<section class="gradient-bg text-white py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-4xl lg:text-5xl font-bold mb-6 leading-tight">
                    Simplify Your Healthcare Practice Tax Management
                </h1>
                <p class="text-xl text-blue-100 mb-8">
                    Connect with certified accountants who understand the unique financial needs of healthcare professionals. Manage taxes, track expenses, and stay compliant effortlessly.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/register?role=healthcare" class="bg-white text-primary-600 px-8 py-4 rounded-lg font-semibold text-center hover:bg-gray-100 transition shadow-lg">
                        <i class="fas fa-user-md mr-2"></i>Join as Healthcare Pro
                    </a>
                    <a href="/register?role=accountant" class="bg-accent-500 text-white px-8 py-4 rounded-lg font-semibold text-center hover:bg-accent-600 transition shadow-lg">
                        <i class="fas fa-calculator mr-2"></i>Join as Accountant
                    </a>
                </div>
            </div>
            <div class="hidden lg:block">
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/20 rounded-xl p-6 text-center">
                            <i class="fas fa-stethoscope text-4xl mb-3"></i>
                            <h3 class="font-semibold">Doctors</h3>
                        </div>
                        <div class="bg-white/20 rounded-xl p-6 text-center">
                            <i class="fas fa-tooth text-4xl mb-3"></i>
                            <h3 class="font-semibold">Dentists</h3>
                        </div>
                        <div class="bg-white/20 rounded-xl p-6 text-center">
                            <i class="fas fa-prescription-bottle-medical text-4xl mb-3"></i>
                            <h3 class="font-semibold">Pharmacies</h3>
                        </div>
                        <div class="bg-white/20 rounded-xl p-6 text-center">
                            <i class="fas fa-file-invoice-dollar text-4xl mb-3"></i>
                            <h3 class="font-semibold">Accountants</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust Indicators -->
<section class="py-12 bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-3xl font-bold text-primary-600">500+</div>
                <div class="text-gray-600">Healthcare Professionals</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-primary-600">100+</div>
                <div class="text-gray-600">Certified Accountants</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-accent-600">$2M+</div>
                <div class="text-gray-600">Tax Savings</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-accent-600">99%</div>
                <div class="text-gray-600">Client Satisfaction</div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section id="how-it-works" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Get started in minutes and simplify your tax management in three easy steps</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-md transition text-center">
                <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold text-white">1</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Create Your Profile</h3>
                <p class="text-gray-600">Sign up as a healthcare professional or accountant. Complete your profile with relevant credentials and business information.</p>
            </div>
            
            <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-md transition text-center">
                <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold text-white">2</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Connect & Upload</h3>
                <p class="text-gray-600">Healthcare pros find and select verified accountants. Upload income documents, expenses, and invoices securely to the platform.</p>
            </div>
            
            <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-md transition text-center">
                <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold text-white">3</span>
                </div>
                <h3 class="text-xl font-semibold mb-4">Get Tax Reports</h3>
                <p class="text-gray-600">Receive comprehensive tax calculations, financial reports, and expert guidance. Download ready-to-file documents anytime.</p>
            </div>
        </div>
    </div>
</section>

<!-- Benefits -->
<section id="benefits" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">Benefits for Healthcare Professionals</h2>
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-primary-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-lg mb-1">Save Time</h4>
                            <p class="text-gray-600">Focus on patient care while experts handle your taxes. No more paperwork headaches.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-accent-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-shield-alt text-accent-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-lg mb-1">Stay Compliant</h4>
                            <p class="text-gray-600">Work with verified accountants who understand healthcare-specific tax regulations.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-chart-line text-primary-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-lg mb-1">Maximize Deductions</h4>
                            <p class="text-gray-600">Expert accountants identify all eligible deductions to minimize your tax liability.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">Benefits for Accountants</h2>
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-accent-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-users text-accent-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-lg mb-1">Grow Your Practice</h4>
                            <p class="text-gray-600">Access a steady stream of healthcare clients looking for specialized tax services.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-folder-open text-primary-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-lg mb-1">Organized Documents</h4>
                            <p class="text-gray-600">All client documents in one secure place. No more chasing paperwork.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-accent-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-handshake text-accent-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-lg mb-1">Simple Billing</h4>
                            <p class="text-gray-600">One flat monthly subscription. No per-client fees. Grow without limits.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing -->
<section id="pricing" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Simple, Transparent Pricing</h2>
            <p class="text-xl text-gray-600">No hidden fees. No surprises.</p>
        </div>
        
        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- Healthcare Professionals -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border-2 border-gray-100">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-md text-primary-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Healthcare Professionals</h3>
                    <p class="text-gray-600">Doctors, Dentists, Pharmacies</p>
                </div>
                <div class="text-center mb-8">
                    <span class="text-5xl font-bold text-gray-900">Free</span>
                    <p class="text-gray-600 mt-2">Platform access is free</p>
                </div>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-3"></i> Unlimited document uploads</li>
                    <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-3"></i> Access to verified accountants</li>
                    <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-3"></i> Tax estimates & reports</li>
                    <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-3"></i> Secure document storage</li>
                    <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-3"></i> Direct messaging</li>
                </ul>
                <a href="/register?role=healthcare" class="block w-full bg-primary-600 text-white text-center py-4 rounded-lg font-semibold hover:bg-primary-700 transition">
                    Get Started Free
                </a>
                <p class="text-center text-sm text-gray-500 mt-4">*Pay accountant fees directly</p>
            </div>
            
            <!-- Accountants -->
            <div class="bg-white rounded-2xl p-8 shadow-lg border-2 border-primary-500 relative">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-primary-500 text-white px-4 py-1 rounded-full text-sm font-medium">
                    Popular
                </div>
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-accent-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calculator text-accent-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Accountants</h3>
                    <p class="text-gray-600">Certified Tax Professionals</p>
                </div>
                <div class="text-center mb-8">
                    <span class="text-5xl font-bold text-gray-900">$80</span>
                    <span class="text-gray-600">/month</span>
                    <p class="text-gray-600 mt-2">Unlimited clients</p>
                </div>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-3"></i> Unlimited client management</li>
                    <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-3"></i> Client document access</li>
                    <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-3"></i> Tax report generation</li>
                    <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-3"></i> Priority support</li>
                    <li class="flex items-center"><i class="fas fa-check text-accent-500 mr-3"></i> Verified badge</li>
                </ul>
                <a href="/register?role=accountant" class="block w-full gradient-bg text-white text-center py-4 rounded-lg font-semibold hover:opacity-90 transition">
                    Start 14-Day Free Trial
                </a>
                <p class="text-center text-sm text-gray-500 mt-4">*12% platform commission on contracts</p>
            </div>
        </div>
    </div>
</section>

<!-- Security & Trust -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Trust & Security</h2>
            <p class="text-xl text-gray-600">Your data is protected with enterprise-grade security</p>
        </div>
        
        <div class="grid md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-lock text-primary-600 text-2xl"></i>
                </div>
                <h4 class="font-semibold mb-2">256-bit Encryption</h4>
                <p class="text-gray-600 text-sm">All data encrypted in transit and at rest</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-check text-accent-600 text-2xl"></i>
                </div>
                <h4 class="font-semibold mb-2">Verified Accountants</h4>
                <p class="text-gray-600 text-sm">All accountants are credential-verified</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-server text-primary-600 text-2xl"></i>
                </div>
                <h4 class="font-semibold mb-2">Secure Storage</h4>
                <p class="text-gray-600 text-sm">Documents stored in secure cloud servers</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-balance-scale text-accent-600 text-2xl"></i>
                </div>
                <h4 class="font-semibold mb-2">HIPAA Compliant</h4>
                <p class="text-gray-600 text-sm">Meeting healthcare data standards</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-20 gradient-bg">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6">Ready to Simplify Your Tax Management?</h2>
        <p class="text-xl text-blue-100 mb-8">Join thousands of healthcare professionals and accountants already using MediTax Connect</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/register" class="bg-white text-primary-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition shadow-lg">
                Get Started Today
            </a>
            <a href="#how-it-works" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/10 transition">
                Learn More
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
