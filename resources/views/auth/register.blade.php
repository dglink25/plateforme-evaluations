<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription - EvalPro</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --light: #f8f9fa;
            --dark: #212529;
        }
        
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 1rem;
        }
        
        .register-card {
            max-width: 450px;
            margin: 0 auto;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .register-header {
            text-align: center;
            padding: 2rem 2rem 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), #7209b7);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(to right, var(--primary), #7209b7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #7209b7);
            border: none;
            padding: 0.75rem;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), #6506a1);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .register-footer {
            text-align: center;
            padding: 1.5rem;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
        }
        
        .register-footer a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .register-footer a:hover {
            text-decoration: underline;
        }
        
        .password-strength {
            height: 5px;
            margin-top: 0.5rem;
            border-radius: 3px;
            background-color: #e9ecef;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .password-requirements {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }
        
        .requirement {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
        }
        
        .requirement i {
            font-size: 0.75rem;
        }
        
        .requirement.valid {
            color: #198754;
        }
        
        .requirement.invalid {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card register-card">
                    <!-- En-tête -->
                    <div class="register-header">
                        <div class="logo">
                            <div class="logo-icon">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                            <div class="logo-text">EvalPro</div>
                        </div>
                        <h2 class="h4 mb-0">Créer votre compte</h2>
                        <p class="text-muted mb-0 mt-2">Rejoignez notre plateforme d'évaluation en ligne</p>
                    </div>

                    <!-- Formulaire -->
                    <form method="POST" action="{{ route('register') }}" id="register-form">
                        @csrf
                        
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <!-- Nom -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-medium">
                                        <i class="bi bi-person me-1"></i>Nom complet
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" 
                                           required 
                                           autofocus
                                           placeholder="Votre nom">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-medium">
                                        <i class="bi bi-envelope me-1"></i>Adresse email
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" 
                                           required
                                           placeholder="votre@email.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Mot de passe -->
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-medium">
                                        <i class="bi bi-lock me-1"></i>Mot de passe
                                    </label>
                                    <input type="password" 
                                           name="password" 
                                           id="password" 
                                           class="form-control @error('password') is-invalid @enderror"
                                           required
                                           placeholder="Créez un mot de passe">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Force du mot de passe -->
                                    <div class="password-strength mt-2">
                                        <div class="password-strength-bar" id="password-strength-bar"></div>
                                    </div>
                                    
                                    <!-- Exigences du mot de passe -->
                                    <div class="password-requirements" id="password-requirements">
                                        <div class="requirement" id="req-length">
                                            <i class="bi bi-circle"></i>
                                            <span>8 caractères minimum</span>
                                        </div>
                                        <div class="requirement" id="req-uppercase">
                                            <i class="bi bi-circle"></i>
                                            <span>1 majuscule</span>
                                        </div>
                                        <div class="requirement" id="req-number">
                                            <i class="bi bi-circle"></i>
                                            <span>1 chiffre</span>
                                        </div>
                                        <div class="requirement" id="req-special">
                                            <i class="bi bi-circle"></i>
                                            <span>1 caractère spécial</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirmation du mot de passe -->
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label fw-medium">
                                        <i class="bi bi-lock-fill me-1"></i>Confirmer le mot de passe
                                    </label>
                                    <input type="password" 
                                           name="password_confirmation" 
                                           id="password_confirmation" 
                                           class="form-control @error('password_confirmation') is-invalid @enderror"
                                           required
                                           placeholder="Confirmez votre mot de passe">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Indicateur de correspondance -->
                                    <div class="mt-2" id="password-match">
                                        <div class="requirement">
                                            <i class="bi bi-circle" id="match-icon"></i>
                                            <span id="match-text">Les mots de passe correspondent</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Conditions -->
                            <div class="mt-4">
                                <div class="form-check">
                                    <input class="form-check-input @error('terms') is-invalid @enderror" 
                                           type="checkbox" 
                                           name="terms" 
                                           id="terms"
                                           required>
                                    <label class="form-check-label" for="terms">
                                        J'accepte les <a href="#" class="text-decoration-none">conditions d'utilisation</a> 
                                        et la <a href="#" class="text-decoration-none">politique de confidentialité</a>
                                    </label>
                                    @error('terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Bouton d'inscription -->
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                                    <i class="bi bi-person-plus me-2"></i>
                                    Créer mon compte
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Pied de page -->
                    <div class="register-footer">
                        <p class="mb-0">
                            Vous avez déjà un compte ? 
                            <a href="{{ route('login') }}" class="fw-medium">Se connecter</a>
                        </p>
                        <p class="mt-2 small">
                            L'inscription est gratuite et ne prend que quelques minutes.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Validation du mot de passe en temps réel
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            const strengthBar = document.getElementById('password-strength-bar');
            const submitBtn = document.getElementById('submit-btn');
            
            // Fonction pour vérifier la force du mot de passe
            function checkPasswordStrength(password) {
                let strength = 0;
                const requirements = {
                    length: false,
                    uppercase: false,
                    number: false,
                    special: false
                };
                
                // Vérifier la longueur
                if (password.length >= 8) {
                    strength += 25;
                    requirements.length = true;
                    document.getElementById('req-length').className = 'requirement valid';
                    document.getElementById('req-length').querySelector('i').className = 'bi bi-check-circle';
                } else {
                    document.getElementById('req-length').className = 'requirement invalid';
                    document.getElementById('req-length').querySelector('i').className = 'bi bi-circle';
                }
                
                // Vérifier les majuscules
                if (/[A-Z]/.test(password)) {
                    strength += 25;
                    requirements.uppercase = true;
                    document.getElementById('req-uppercase').className = 'requirement valid';
                    document.getElementById('req-uppercase').querySelector('i').className = 'bi bi-check-circle';
                } else {
                    document.getElementById('req-uppercase').className = 'requirement invalid';
                    document.getElementById('req-uppercase').querySelector('i').className = 'bi bi-circle';
                }
                
                // Vérifier les chiffres
                if (/[0-9]/.test(password)) {
                    strength += 25;
                    requirements.number = true;
                    document.getElementById('req-number').className = 'requirement valid';
                    document.getElementById('req-number').querySelector('i').className = 'bi bi-check-circle';
                } else {
                    document.getElementById('req-number').className = 'requirement invalid';
                    document.getElementById('req-number').querySelector('i').className = 'bi bi-circle';
                }
                
                // Vérifier les caractères spéciaux
                if (/[^A-Za-z0-9]/.test(password)) {
                    strength += 25;
                    requirements.special = true;
                    document.getElementById('req-special').className = 'requirement valid';
                    document.getElementById('req-special').querySelector('i').className = 'bi bi-check-circle';
                } else {
                    document.getElementById('req-special').className = 'requirement invalid';
                    document.getElementById('req-special').querySelector('i').className = 'bi bi-circle';
                }
                
                // Mettre à jour la barre de progression
                strengthBar.style.width = strength + '%';
                
                // Changer la couleur selon la force
                if (strength < 50) {
                    strengthBar.style.backgroundColor = '#dc3545';
                } else if (strength < 75) {
                    strengthBar.style.backgroundColor = '#ffc107';
                } else {
                    strengthBar.style.backgroundColor = '#198754';
                }
                
                return requirements;
            }
            
            // Vérifier si les mots de passe correspondent
            function checkPasswordMatch() {
                const password = passwordInput.value;
                const confirmPassword = confirmInput.value;
                const matchIcon = document.getElementById('match-icon');
                const matchText = document.getElementById('match-text');
                
                if (confirmPassword === '') {
                    matchIcon.className = 'bi bi-circle';
                    matchText.textContent = 'Les mots de passe correspondent';
                    matchIcon.parentElement.parentElement.className = 'requirement';
                    return false;
                }
                
                if (password === confirmPassword) {
                    matchIcon.className = 'bi bi-check-circle';
                    matchText.textContent = 'Les mots de passe correspondent';
                    matchIcon.parentElement.parentElement.className = 'requirement valid';
                    return true;
                } else {
                    matchIcon.className = 'bi bi-x-circle';
                    matchText.textContent = 'Les mots de passe ne correspondent pas';
                    matchIcon.parentElement.parentElement.className = 'requirement invalid';
                    return false;
                }
            }
            
            // Écouter les changements dans les champs de mot de passe
            passwordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                checkPasswordMatch();
            });
            
            confirmInput.addEventListener('input', checkPasswordMatch);
            
            // Validation du formulaire
            document.getElementById('register-form').addEventListener('submit', function(e) {
                const password = passwordInput.value;
                const confirmPassword = confirmInput.value;
                const terms = document.getElementById('terms').checked;
                
                // Vérifier la force du mot de passe
                const requirements = checkPasswordStrength(password);
                const allRequirementsMet = Object.values(requirements).every(req => req);
                
                if (!allRequirementsMet) {
                    e.preventDefault();
                    alert('Veuillez choisir un mot de passe plus fort qui respecte toutes les exigences.');
                    return false;
                }
                
                // Vérifier la correspondance des mots de passe
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Les mots de passe ne correspondent pas.');
                    return false;
                }
                
                // Vérifier les conditions
                if (!terms) {
                    e.preventDefault();
                    alert('Veuillez accepter les conditions d\'utilisation.');
                    return false;
                }
                
                // Animation de chargement
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Création du compte en cours...
                `;
            });
            
            // Afficher/masquer les mots de passe
            function setupPasswordToggle(inputId, buttonId) {
                const input = document.getElementById(inputId);
                const button = document.getElementById(buttonId);
                
                button.addEventListener('click', function() {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
                });
            }
            
            // Ajouter les boutons pour afficher/masquer
            function addPasswordToggle(inputId) {
                const input = document.getElementById(inputId);
                const container = input.parentNode;
                const toggleButton = document.createElement('button');
                
                toggleButton.type = 'button';
                toggleButton.id = `toggle-${inputId}`;
                toggleButton.className = 'btn btn-outline-secondary position-absolute end-0 top-50 translate-middle-y';
                toggleButton.style.cssText = 'border: none; background: none; z-index: 5; padding: 0.375rem 0.75rem;';
                toggleButton.innerHTML = '<i class="bi bi-eye"></i>';
                
                container.style.position = 'relative';
                container.appendChild(toggleButton);
                
                setupPasswordToggle(inputId, `toggle-${inputId}`);
            }
            
            // Ajouter les boutons pour les deux champs de mot de passe
            addPasswordToggle('password');
            addPasswordToggle('password_confirmation');
        });
    </script>
</body>
</html>