php artisan make:livewire Auth/Login
php artisan make:livewire Auth/Logout
php artisan make:livewire Auth/Register
php artisan make:livewire Auth/ConfirmPassword
php artisan make:livewire Auth/TwoFactorChallenge
php artisan make:livewire Auth/ResetPassword
php artisan make:livewire Auth/ForgotPassword
php artisan make:livewire Auth/VerifyEmail

php artisan make:livewire Profile/Show
php artisan make:livewire Profile/TwoFactorPage

php artisan make:middleware EnsureTwoFactorIsEnabled
php artisan make:middleware ThrottleLoginAttempts

 php artisan make:middleware SecurityHeaders


php artisan make:livewire Church/Only/OnlyChurches
php artisan make:livewire Church/Only/BibleReading
php artisan make:livewire Church/Only/ChuchGoodPractices
php artisan make:livewire Church/Only/ChurchPastoral
php artisan make:livewire Church/Members/Members
php artisan make:livewire Church/Ministries/Ministries
php artisan make:livewire Church/Events/Events
php artisan make:livewire Church/Events/Schedules
php artisan make:livewire Church/Events/Scale
php artisan make:livewire Church/Events/StandardCult
php artisan make:livewire Church/Members/TalentMap
php artisan make:livewire Church/Events/Calendar
php artisan make:livewire Church/Courses/Courses
php artisan make:livewire Church/Courses/Certificates
php artisan make:livewire Church/Courses/ProgressReport
php artisan make:livewire Church/Fincance/FinancialMoviment
php artisan make:livewire Church/Fincance/FinancialReport
php artisan make:livewire Church/Fincance/OnlineDonations
php artisan make:livewire Church/Members/Volunteers
php artisan make:livewire Church/Settings/Resources
php artisan make:livewire Church/Only/PastoralCare
php artisan make:livewire Church/Marketplace/Products
php artisan make:livewire Church/Marketplace/Orders
php artisan make:livewire Church/Marketplace/Payments
php artisan make:livewire Church/Members/Statistics

php artisan make:livewire Church/Orders/SpecialRequests
php artisan make:livewire Church/Engagement/Badges
php artisan make:livewire Church/Engagement/Points
php artisan make:livewire Church/Engagement/Polls
php artisan make:livewire Church/Billing/Subscription



# Definição de pacotes e permissões
php artisan make:livewire Billing/Pacotes
php artisan make:livewire Billing/Modulos
php artisan make:livewire Billing/PermissoesPacote

# Gestão de assinaturas (lado admin)
php artisan make:livewire Billing/Assinaturas/Atual
php artisan make:livewire Billing/Assinaturas/Historico
php artisan make:livewire Billing/Assinaturas/Pagamentos
php artisan make:livewire Billing/Assinaturas/Faturas
php artisan make:livewire Billing/Assinaturas/Notificacoes
php artisan make:livewire Billing/Assinaturas/Upgrades





php artisan make:model CategoriaIgreja
php artisan make:model AliancaIgreja
php artisan make:model CursoTurma
php artisan make:model CursoMatricula
php artisan make:model CursoCertificado
php artisan make:model PedidoTipo
php artisan make:model PedidoEspecial
