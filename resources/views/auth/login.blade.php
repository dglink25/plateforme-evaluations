<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - EvalPro</title>
    
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
        
        .login-card {
            max-width: 400px;
            margin: 0 auto;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .login-header {
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
        
        .login-footer {
            text-align: center;
            padding: 1.5rem;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
        }
        
        .login-footer a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            border: none;
            border-radius: 0.5rem;
        }
        
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .form-check-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card login-card">
                    <!-- En-tête -->
                    <div class="login-header">
                        <div class="logo">
                            <div class="logo-icon">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                            <div class="logo-text">EvalPro</div>
                        </div>
                        <h2 class="h4 mb-0">Connexion à votre compte</h2>
                    </div>

                    <!-- Messages -->
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Formulaire -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="card-body p-4">
                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label fw-medium">
                                    <i class="bi bi-envelope me-1"></i>Adresse email
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus
                                       placeholder="votre@email.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mot de passe -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="password" class="form-label fw-medium">
                                        <i class="bi bi-lock me-1"></i>Mot de passe
                                    </label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="small text-decoration-none">
                                            Mot de passe oublié ?
                                        </a>
                                    @endif
                                </div>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       class="form-control @error('password') is-invalid @enderror"
                                       required
                                       placeholder="Votre mot de passe">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Se souvenir de moi -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="remember" 
                                           id="remember">
                                    <label class="form-check-label" for="remember">
                                        Se souvenir de moi
                                    </label>
                                </div>
                            </div>

                            <!-- Bouton de connexion -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Se connecter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Pied de page -->
                    <div class="login-footer">
                        <p class="mb-0">
                            Pas encore de compte ? 
                            <a href="{{ route('register') }}" class="fw-medium">S'inscrire</a>
                        </p>
                        <p class="mt-2 small">
                            En vous connectant, vous acceptez nos conditions d'utilisation.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Animation de chargement
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Connexion en cours...
            `;
            
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 5000);
        });

        // Afficher/masquer le mot de passe
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.createElement('button');
            
            togglePassword.type = 'button';
            togglePassword.className = 'btn btn-outline-secondary position-absolute end-0 top-50 translate-middle-y me-3';
            togglePassword.style.cssText = 'border: none; background: none; z-index: 5;';
            togglePassword.innerHTML = '<i class="bi bi-eye"></i>';
            
            // Ajouter le bouton après le champ de mot de passe
            const passwordContainer = passwordInput.parentNode;
            passwordContainer.style.position = 'relative';
            passwordContainer.appendChild(togglePassword);
            
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
            });
        });
    </script>
</body>
</html>