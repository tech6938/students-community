@extends('layouts.app')

@section('title', 'FreelancerHub | Sign Up')

@section('content')
<div class="signup-container">
    <div class="signup-card">
        <div class="signup-header">
            <div class="brand-header">
                <i class="fas fa-code brand-icon"></i>
                <div class="logo">Freelancer<span>Hub</span></div>
            </div>
            <h2>Join FreelancerHub</h2>
            <p>Create your account and start your freelance journey</p>
        </div>

        <div class="signup-body">
            <div class="user-type-tabs">
                <button type="button" class="user-tab active" data-type="creative">
                    <i class="fas fa-palette"></i>
                    Creative
                </button>
                <button type="button" class="user-tab" data-type="company">
                    <i class="fas fa-building"></i>
                    Company
                </button>
            </div>

            <form id="signupForm" method="POST" action="{{ route('signup.submit') }}">
                @csrf
                <input type="hidden" name="user_type" id="userType" value="creative">
                
                <div class="form-group" id="creativeNameField">
                    <label for="creativeName" class="form-label">Your Name</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input
                            type="text"
                            class="form-control"
                            id="creativeName"
                            name="name"
                            placeholder="Your name"
                            required
                        />
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            placeholder="name@example.com"
                            required
                        />
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Create a strong password"
                            required
                        />
                        <span class="input-group-text password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div class="password-strength">
                        <span id="strengthText">Password strength: </span>
                        <div class="strength-meter">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input
                            type="password"
                            class="form-control"
                            id="confirmPassword"
                            name="password_confirmation"
                            placeholder="Confirm your password"
                            required
                        />
                        <span class="input-group-text password-toggle" id="toggleConfirmPassword">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div id="passwordMatch" class="mt-1" style="font-size: 0.8rem;"></div>
                </div>

                <!-- Creative Profile Section -->
                <div class="profile-section active" id="creativeSection">
                    <div class="form-group">
                        <label for="experienceLevel" class="form-label">Experience Level</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="experienceLevel"
                            name="experience_level"
                            placeholder="e.g., 3 years, Expert, Intermediate"
                            required
                        />
                        <div class="skills-hint">
                            Describe your experience level (e.g., Beginner, Intermediate, Expert)
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="availability" class="form-label">Availability</label>
                        <select class="form-control" id="availability" name="availability" required>
                            <option value="" disabled selected>Select your availability</option>
                            <option value="available">Available (Open for work)</option>
                            <option value="busy">Currently Busy (Limited availability)</option>
                            <option value="parttime">Part-time (Limited hours)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="skills" class="form-label">Skills</label>
                        <textarea
                            class="form-control skills-input"
                            id="skills"
                            name="skills"
                            rows="3"
                            placeholder="Enter your skills separated by commas (e.g., React, Node.js, UI/UX Design)"
                            required
                        ></textarea>
                        <div class="skills-hint">Separate each skill with a comma</div>
                    </div>

                    <div class="form-group">
                        <label for="workTypes" class="form-label">Desired Work Type</label>
                        <input
                            type="text"
                            class="form-control"
                            id="workTypes"
                            name="work_types"
                            placeholder="e.g., Full-time, Contract, Remote"
                            required
                        />
                        <div class="skills-hint">Separate work types with commas if multiple</div>
                    </div>

                    <div class="form-group optional-field">
                        <label for="portfolioLinks" class="form-label">Portfolio Links</label>
                        <textarea
                            class="form-control"
                            id="portfolioLinks"
                            name="portfolio_links"
                            rows="2"
                            placeholder="Enter your portfolio links (one per line)"
                        ></textarea>
                        <div class="skills-hint">Enter one URL per line</div>
                    </div>
                </div>

                <!-- Company Profile Section -->
                <div class="profile-section" id="companySection">
                    <div class="form-group">
                        <label for="companyName" class="form-label">Company Name</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-building"></i>
                            </span>
                            <input
                                type="text"
                                class="form-control"
                                id="companyName"
                                name="company_name"
                                placeholder="Your Company Inc."
                            />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="industry" class="form-label">Industry</label>
                        <input
                            type="text"
                            class="form-control"
                            id="industry"
                            name="industry"
                            placeholder="e.g., Technology, Finance, Healthcare"
                        />
                        <div class="skills-hint">Enter your company's industry</div>
                    </div>

                    <div class="form-group">
                        <label for="roleDescription" class="form-label">Role Description</label>
                        <textarea
                            class="form-control"
                            id="roleDescription"
                            name="role_description"
                            rows="3"
                            placeholder="Describe the role you're hiring for..."
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label for="requiredSkills" class="form-label">Required Skills</label>
                        <textarea
                            class="form-control skills-input"
                            id="requiredSkills"
                            name="required_skills"
                            rows="3"
                            placeholder="Enter required skills separated by commas (e.g., JavaScript, React, AWS)"
                        ></textarea>
                        <div class="skills-hint">Separate each skill with a comma</div>
                    </div>

                    <div class="form-group">
                        <label for="budget" class="form-label">Budget Range (per hour)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input
                                type="number"
                                class="form-control"
                                id="budget"
                                name="budget"
                                placeholder="50"
                                min="10"
                                max="500"
                                step="10"
                            />
                            <span class="input-group-text">/hour</span>
                        </div>
                        <div class="skills-hint">Enter your budget per hour (e.g., 50)</div>
                    </div>

                    <div class="form-group">
                        <label for="engagementType" class="form-label">Engagement Type</label>
                        <input
                            type="text"
                            class="form-control"
                            id="engagementType"
                            name="engagement_type"
                            placeholder="e.g., Contract, Full-time, Remote"
                        />
                        <div class="skills-hint">Separate engagement types with commas if multiple</div>
                    </div>
                </div>

                <div class="terms-check">
                    <input class="form-check-input" type="checkbox" id="termsAgree" name="terms" required>
                    <label class="form-check-label" for="termsAgree">
                        I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. 
                        I understand that I can update my preferences at any time.
                    </label>
                </div>

                <button type="submit" class="btn btn-primary mb-4">
                    <i class="fas fa-user-plus me-2"></i> Create Account
                </button>
            </form>

            <div class="text-center">
                <p class="mb-0">
                    Already have an account?
                    <a href="{{ route('login') }}" class="signup-link">Sign in here</a>
                </p>
            </div>
        </div>

        <div class="signup-footer">
            <p class="small mb-0">
                Join thousands of creatives and companies already on FreelancerHub
            </p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* All signup page styles from your original HTML */
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .signup-container {
        width: 100%;
        max-width: 600px;
    }

    .signup-card {
        background-color: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(67, 97, 238, 0.15);
        width: 100%;
        overflow: hidden;
    }

    .signup-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        padding: 2.5rem 2rem;
        text-align: center;
        color: white;
    }

    .signup-header h2 {
        color: white;
        margin-bottom: 0.5rem;
    }

    .signup-header p {
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 0;
        font-size: 0.95rem;
    }

    .logo {
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: inline-block;
    }

    .logo span {
        color: var(--accent-color);
    }

    .signup-body {
        padding: 2.5rem 2rem 2rem;
    }

    .signup-footer {
        padding: 1.5rem 2rem;
        text-align: center;
        background-color: rgba(248, 249, 250, 0.7);
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .form-label {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-control {
        padding: 0.75rem 1rem;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        transition: all 0.3s;
        font-size: 1rem;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
    }

    .input-group-text {
        background-color: #f8f9fa;
        border: 2px solid #e9ecef;
        border-right: none;
        color: var(--gray-color);
    }

    .password-toggle {
        background-color: #f8f9fa;
        border: 2px solid #e9ecef;
        border-left: none;
        cursor: pointer;
        color: var(--gray-color);
    }

    .password-toggle:hover {
        color: var(--primary-color);
    }

    .btn-primary {
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        border: none;
        padding: 0.85rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s;
        width: 100%;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
    }

    .signup-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s;
    }

    .signup-link:hover {
        color: var(--secondary-color);
        text-decoration: underline;
    }

    .brand-header {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .brand-icon {
        font-size: 2.2rem;
        margin-right: 0.5rem;
        color: var(--accent-color);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .password-strength {
        margin-top: 0.5rem;
        font-size: 0.8rem;
    }

    .strength-meter {
        height: 5px;
        background-color: #e9ecef;
        border-radius: 3px;
        margin-top: 0.3rem;
        overflow: hidden;
    }

    .strength-fill {
        height: 100%;
        width: 0%;
        border-radius: 3px;
        transition: all 0.3s;
    }

    .strength-weak {
        background-color: #dc3545;
        width: 25%;
    }

    .strength-fair {
        background-color: #ffc107;
        width: 50%;
    }

    .strength-good {
        background-color: #17a2b8;
        width: 75%;
    }

    .strength-strong {
        background-color: #28a745;
        width: 100%;
    }

    .terms-check {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }

    .terms-check .form-check-input {
        margin-top: 0.3rem;
        margin-right: 0.5rem;
    }

    .terms-check label {
        font-size: 0.85rem;
        color: var(--gray-color);
        line-height: 1.4;
    }

    .terms-check label a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
    }

    .terms-check label a:hover {
        text-decoration: underline;
    }

    .user-type-tabs {
        display: flex;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        border: 2px solid #e9ecef;
    }

    .user-tab {
        flex: 1;
        padding: 1rem;
        text-align: center;
        background-color: white;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
        color: var(--gray-color);
        border: none;
    }

    .user-tab:hover {
        background-color: rgba(67, 97, 238, 0.05);
    }

    .user-tab.active {
        background-color: var(--primary-color);
        color: white;
    }

    .user-tab i {
        display: block;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .profile-section {
        display: none;
        animation: fadeIn 0.3s ease-out;
    }

    .profile-section.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .skills-input {
        margin-top: 0.5rem;
    }

    .skills-hint {
        font-size: 0.8rem;
        color: var(--gray-color);
        margin-top: 0.3rem;
    }

    .optional-field {
        opacity: 0.8;
    }

    .optional-field .form-label::after {
        content: " (Optional)";
        font-weight: normal;
        color: var(--gray-color);
    }

    @media (max-width: 768px) {
        .signup-header {
            padding: 2rem 1.5rem;
        }
        
        .signup-body {
            padding: 2rem 1.5rem 1.5rem;
        }
        
        .logo {
            font-size: 1.8rem;
        }
        
        .signup-container {
            max-width: 450px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // User type tabs
        const userTabs = document.querySelectorAll(".user-tab");
        const creativeSection = document.getElementById("creativeSection");
        const companySection = document.getElementById("companySection");
        const creativeNameField = document.getElementById("creativeNameField");
        const userTypeInput = document.getElementById("userType");

        userTabs.forEach((tab) => {
            tab.addEventListener("click", function () {
                // Update active tab
                userTabs.forEach((t) => t.classList.remove("active"));
                this.classList.add("active");

                // Show corresponding section
                const type = this.dataset.type;
                userTypeInput.value = type;
                
                if (type === "creative") {
                    creativeSection.classList.add("active");
                    companySection.classList.remove("active");
                    creativeNameField.style.display = "block";
                    
                    // Set required fields for creative
                    document.getElementById('experienceLevel').required = true;
                    document.getElementById('availability').required = true;
                    document.getElementById('skills').required = true;
                    document.getElementById('workTypes').required = true;
                    
                    // Remove required from company fields
                    document.getElementById('companyName').required = false;
                    document.getElementById('industry').required = false;
                    document.getElementById('roleDescription').required = false;
                    document.getElementById('requiredSkills').required = false;
                    document.getElementById('budget').required = false;
                    document.getElementById('engagementType').required = false;
                } else {
                    creativeSection.classList.remove("active");
                    companySection.classList.add("active");
                    creativeNameField.style.display = "none";
                    
                    // Remove required from creative fields
                    document.getElementById('experienceLevel').required = false;
                    document.getElementById('availability').required = false;
                    document.getElementById('skills').required = false;
                    document.getElementById('workTypes').required = false;
                    
                    // Set required fields for company
                    document.getElementById('companyName').required = true;
                    document.getElementById('industry').required = true;
                    document.getElementById('roleDescription').required = true;
                    document.getElementById('requiredSkills').required = true;
                    document.getElementById('budget').required = true;
                    document.getElementById('engagementType').required = true;
                }
            });
        });

        // Password toggle functions
        function setupPasswordToggle(toggleBtn, inputField) {
            toggleBtn.addEventListener("click", function () {
                const type = inputField.getAttribute("type") === "password" ? "text" : "password";
                inputField.setAttribute("type", type);

                const eyeIcon = this.querySelector("i");
                if (type === "password") {
                    eyeIcon.classList.remove("fa-eye-slash");
                    eyeIcon.classList.add("fa-eye");
                } else {
                    eyeIcon.classList.remove("fa-eye");
                    eyeIcon.classList.add("fa-eye-slash");
                }
            });
        }

        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");
        const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");
        const confirmPasswordInput = document.getElementById("confirmPassword");

        if (togglePassword) setupPasswordToggle(togglePassword, passwordInput);
        if (toggleConfirmPassword) setupPasswordToggle(toggleConfirmPassword, confirmPasswordInput);

        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            const strengthFill = document.getElementById("strengthFill");
            const strengthText = document.getElementById("strengthText");

            if (!strengthFill || !strengthText) return;

            strengthFill.className = "strength-fill";

            if (!password) {
                strengthText.textContent = "Password strength: ";
                return;
            }

            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            if (strength <= 2) {
                strengthFill.className = "strength-fill strength-weak";
                strengthText.textContent = "Password strength: Weak";
            } else if (strength <= 4) {
                strengthFill.className = "strength-fill strength-fair";
                strengthText.textContent = "Password strength: Fair";
            } else if (strength <= 6) {
                strengthFill.className = "strength-fill strength-good";
                strengthText.textContent = "Password strength: Good";
            } else {
                strengthFill.className = "strength-fill strength-strong";
                strengthText.textContent = "Password strength: Strong";
            }
        }

        // Check password match
        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const matchDiv = document.getElementById("passwordMatch");

            if (!matchDiv) return;

            if (!confirmPassword) {
                matchDiv.textContent = "";
                matchDiv.style.color = "";
                return;
            }

            if (password === confirmPassword) {
                matchDiv.textContent = "✓ Passwords match";
                matchDiv.style.color = "var(--success-color)";
            } else {
                matchDiv.textContent = "✗ Passwords do not match";
                matchDiv.style.color = "#dc3545";
            }
        }

        if (passwordInput) {
            passwordInput.addEventListener("input", function () {
                checkPasswordStrength(this.value);
                checkPasswordMatch();
            });
        }

        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener("input", checkPasswordMatch);
        }
    });
</script>
@endpush