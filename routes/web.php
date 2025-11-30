<?php


//** OTHER COMPONENTS */
use App\Models\User;
use App\Livewire\Auth\Login;
use Illuminate\Http\Request;
use App\Livewire\Users\Users;
use App\Livewire\Church\Posts;
use App\Livewire\Billings\Logs;
use App\Livewire\Billings\Cupons;

//** LOGIN USER */
use App\Livewire\Super\Dashboard;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Billings\Modulos;
use App\Livewire\Billings\Pacotes;
use App\Livewire\Auth\SelectChurch;
use App\Livewire\Auth\ResetPassword;
use Illuminate\Auth\Events\Verified;


//** USERS */
use Illuminate\Support\Facades\Auth;

//** ADMIN COMPONENTS */
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Billings\Pagamentos;
use App\Livewire\Church\Events\Scale;

//** BILLINGS COMPONENTS */
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\ConfirmPassword;
use App\Livewire\Church\Events\Events;
use App\Livewire\Admin\DashboardMember;
use App\Livewire\Billings\Notificacoes;
use App\Livewire\Church\ChurchShowcase;
use App\Livewire\Profile\TwoFactorPage;
use App\Livewire\Church\Courses\Courses;
use App\Livewire\Church\Events\Calendar;
use App\Livewire\Church\Members\Members;
use App\Livewire\Church\Reports\Reports;
use App\Livewire\Auth\TwoFactorChallenge;

//** START CHURCHES COMPONENTS */
use App\Livewire\Church\Engagement\Polls;
use App\Livewire\Church\Events\Schedules;
use App\Livewire\Church\Engagement\Badges;
use App\Livewire\Church\Engagement\Points;
use App\Livewire\Church\Members\TalentMap;
use App\Livewire\Church\Only\BibleReading;
use App\Livewire\Church\Only\OnlyChurches;
use App\Livewire\Church\Only\PastoralCare;
use App\Livewire\Subscription\PaymentPage;
use App\Livewire\Subscription\UpgradePage;
use App\Livewire\Ecommerce\Home as EcommerceHome;
use App\Livewire\Billings\IgrejasAssinadas;
use App\Livewire\Billings\PacotePermissoes;
use App\Livewire\Church\Alliance\Community;
use App\Livewire\Church\Marketplace\Orders;
use App\Livewire\Church\Members\Statistics;
use App\Livewire\Church\Members\Volunteers;
use App\Livewire\Church\Settings\Resources;
use App\Livewire\Billings\AssinaturasAtuais;
use App\Livewire\Church\Alliance\ChurchChat;
use App\Livewire\Church\Alliance\MyAlliance;
use App\Livewire\Church\Courses\CourseClass;
use App\Livewire\Church\Events\StandardCult;
use App\Livewire\Church\Members\MemberCards;
use App\Livewire\Church\Alliance\PrivateChat;
use App\Livewire\Church\Billing\Subscription;
use App\Livewire\Church\Courses\Certificates;
use App\Livewire\Church\Marketplace\Payments;
use App\Livewire\Church\Marketplace\Products;
use App\Livewire\Profile\Show as ProfileShow;
use App\Livewire\Church\Ministries\Ministries;
use App\Livewire\Billings\AssinaturasHistorico;
use App\Livewire\Church\Orders\SpecialRequests;
use App\Livewire\Church\Alliance\AllianceChurch;
use App\Livewire\Church\Finance\FinancialReport;
use App\Livewire\Church\Finance\OnlineDonations;
use App\Livewire\Church\Only\ChuchGoodPractices;
use App\Livewire\Church\Courses\CourseRegistered;
use App\Livewire\Church\Finance\FinancialAccount;
use App\Livewire\Root\Dashboard as RootDashboard;
use App\Livewire\Church\Finance\FinancialMoviment;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Church\LeadershipBody\LeadershipBody;
use App\Livewire\Billings\Calendar as BillingsCalendar;
use App\Livewire\Billings\Subscribers;
use App\Livewire\Ecommerce\Alliance;
use App\Livewire\Ecommerce\Church;
use App\Livewire\Ecommerce\Contact;
use App\Livewire\Ecommerce\PaymentAssignatureChurche;
use App\Livewire\Ecommerce\WhoWeAre;

//** END CHURCHES COMPONENTS */




Route::get('/', function () {
    return Auth::check()
        ? redirect()->route(Auth::user()->redirectDashboardRoute())
        : redirect('/e-commerce');
});


//** PUBLIC ROUTES */

Route::get('/login', Login::class)->name('login')->middleware(['guest', \App\Http\Middleware\VerifyCsrfToken::class]);

//** E-COMMERCE ROUTES (accessible for both logged and non-logged users) */
Route::prefix('e-commerce')->name('ecommerce.')->group(function () {
    // Home page - accessible for everyone
    Route::get('/', EcommerceHome::class)->name('home');
    Route::get('/churches', Church::class)->name('churches');
    Route::get('/alliance', Alliance::class)->name('alliance');
    Route::get('/payments-assignatures', PaymentAssignatureChurche::class)->name('payment.assignature')->middleware(['auth']);
    Route::get('/contacts', Contact::class)->name('contact');
    Route::get('/who-we-are', WhoWeAre::class)->name('who.we');

    //* TRIAL ROUTES
    Route::get('/trial-expirando', \App\Livewire\Ecommerce\Trial\TrialExpirando::class)->name('trial.expirando')->middleware(['auth']);
    Route::get('/trial-criado', \App\Livewire\Ecommerce\Trial\TrialCriado::class)->name('trial.criado')->middleware(['auth']);
    Route::get('/trial-solicitar', \App\Livewire\Ecommerce\Trial\TrialSolicitar::class)->name('trial.solicitar');

    //* SUBSCRIPTION UPGRADE PAGE
    Route::get('/subscription-upgrade/{igreja?}', UpgradePage::class)->name('subscription.upgrade')->middleware(['checkSubscription']);

    //* SUBSCRIPTION PAYMENT PAGE
    Route::get('/subscription-payment/{pacote}/{acao?}', PaymentPage::class)->name('subscription.payment');

});


//** */ Route::get('/register', Register::class)->name('register');
Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
Route::get('/verify-email', VerifyEmail::class)->name('verification.notice');
Route::get('/selecionar-igreja', SelectChurch::class)->name('selecionar.igreja')->middleware(['auth', 'verified', '2fa']);
Route::get('/email/verify/{id}/{hash}', function (Request $request) {
    $user = User::find($request->route('id'));

    if (!$user) {
        #=> USER NOT FOUND → redireciona pro login
        return redirect()->route('login')->with('error', 'Usuário inválido.');
    }

    if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
        # => hash inválida → redireciona pro login
        return redirect()->route('login')->with('login_error', 'Link de verificação inválido.');
    }
    // loga o user
    Auth::login($user, true);

    if (!$user->hasVerifiedEmail() && $user->markEmailAsVerified()) {
        event(new Verified($user));
    }

    return redirect()->route($user->redirectDashboardRoute())
        ->with('status', 'Email verificado com sucesso!');
})->middleware(['auth','signed'])->name('verification.verify');

// Rota de desafio 2FA (acessível apenas se 2FA estiver ativado)
Route::get('/two-factor-challenge', TwoFactorChallenge::class)->middleware(['auth', '2fa-challenge'])->name('two-factor.login');

Route::get('/confirm-password', ConfirmPassword::class)->name('password.confirm');


//**AUTHORIZE ROUTES**/
Route::middleware(['auth', \App\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    // Rotas que requerem email verificado

     //** */ Rotas de 2FA (não requerem 2FA completo, mas requerem email verificado)
    Route::middleware(['verified'])->group(function () {


    Route::get('/profile', ProfileShow::class)->name('profile.show')->middleware(['checkSubscription']);



    // Rotas que requerem 2FA completo
    Route::middleware(['2fa', 'checkUserStatus', 'checkSelectedChurch', 'checkSubscription', 'checkValidPermissions'])->group(function () {

        # => Route for Admin Or Root
        Route::get('dashboard-administrative', Dashboard::class)->name('dashboard.administrative')->middleware(['isSuperAdmin']);
        Route::get('dashboard-church', AdminDashboard::class)->name('dashboard-admin.church')->middleware(['isAdminIgreja']);
        Route::get('dashboard-member', DashboardMember::class)->name('dashboard.member');
        Route::get('dashboard-root', RootDashboard::class)->name('dashboard.root')->middleware(['isRoot']);
        Route::get('geral/list-users', Users::class)->name('users.lisusers')->middleware(['isSuperAdmin']);

          //** ROTA DE IMPRESSÃO DE CARTÃO (fora dos middlewares para evitar problemas) */
        Route::get('churches/church-member-cards-print/{cartaoId}', function ($cartaoId) {
            $cartao = \App\Models\CartaoMembro\CartaoMembro::with(['membro.user'])->find($cartaoId);
            $igreja = Auth::user()->getIgreja();

            if (!$cartao || $cartao->igreja_id !== $igreja->id) {
                abort(404, 'Cartão não encontrado');
            }

            // Registar a impressão na base de dados
            $cartao->update([
                'impresso_em' => now(),
                'impresso_por' => Auth::id(),
            ]);
            \App\Models\CartaoMembro\CartaoMembroHistorico::registrarAcao(
                $cartao,
                \App\Models\CartaoMembro\CartaoMembroHistorico::ACAO_IMPRESSO,
                'Cartão impresso',
                Auth::user()
            );

            // Converter a imagem para Base64 para embutir no SVG
            $foto_base64 = null;
            if ($cartao->foto_url && file_exists(public_path($cartao->foto_url))) {
                $path = public_path($cartao->foto_url);
                $type = pathinfo($path, PATHINFO_EXTENSION);
                if (in_array(strtolower($type), ['jpg', 'jpeg', 'png', 'gif'])) {
                    $data = file_get_contents($path);
                    $foto_base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }

            // Determinar a cor do status para o SVG
            $statusColor = [
                'ativo' => '#10B981',
                'inativo' => '#dc3545',
                'perdido' => '#fd7e14',
                'danificado' => '#6f42c1',
                'renovado' => '#20c997',
                'cancelado' => '#6c757d',
            ][$cartao->status] ?? '#6c757d';

            // Renderizar a view do SVG
            $svgContent = view('church.members.pdf.member-card', [
                'cartao' => $cartao,
                'igreja' => $igreja,
                'foto_base64' => $foto_base64,
                'statusColor' => $statusColor
            ])->render();

            return view('church.members.print.member-card', [
                'cartao' => $cartao,
                'svgContent' => $svgContent
            ]);
        })->name('churches.member-cards.print');


        //** BILLINGS ROUTES OF ASSIGANTURES */
        Route::middleware(['isSuperAdmin'])->group(function(){

            Route::prefix('admin/assignatures')->name('admin.assignatures.')->group(function(){
                Route::get('subscribers', Subscribers::class)->name('subscribers');
                Route::get('pacotes', Pacotes::class)->name('pacotes');
                Route::get('assinaturas-atuais', AssinaturasAtuais::class)->name('assinaturas-atuais');
                Route::get('assinaturas-historico', AssinaturasHistorico::class)->name('assinaturas-historico');
                Route::get('modulos', Modulos::class)->name('modulos');
                Route::get('pacote-permissoes', PacotePermissoes::class)->name('pacote-permissoes');
                Route::get('pagamentos/{assinatura_id?}', Pagamentos::class)->name('pagamentos');
                Route::get('igrejas-assinadas', IgrejasAssinadas::class)->name('igrejas-assinadas');
                Route::get('logs', Logs::class)->name('logs');
                Route::get('cupons', Cupons::class)->name('cupons');
                Route::get('notifications', Notificacoes::class)->name('notifications');
                Route::get('calendar', BillingsCalendar::class)->name('calendar');

                // NOVAS ROTAS SAAS
                Route::get('alertas', \App\Livewire\Billings\Alertas::class)->name('alertas');
                Route::get('recursos-bloqueados', \App\Livewire\Billings\RecursosBloqueados::class)->name('recursos-bloqueados');
                Route::get('pacote-recursos', \App\Livewire\Billings\PacoteRecursos::class)->name('pacote-recursos');
                Route::get('pacote-niveis', \App\Livewire\Billings\PacoteNiveis::class)->name('pacote-niveis');
                Route::get('trial-requests', \App\Livewire\Billings\Trial\TrialRequests::class)->name('trial-requests');
            });

            Route::prefix('admin')->name('admin.')->group(function(){
                Route::get('/church', \App\Livewire\Church\Only\AdminChurch::class)->name('church');
                Route::get('/admin-church/{churchId?}', \App\Livewire\Users\AdminChurch::class)->name('admin-church');
            });
        
        
        });
   

         //**PREFIX CHURCHES  */
        Route::prefix('churches/')->name('churches.')->group(function(){
            Route::get('only-churches', OnlyChurches::class)->name('churches.only');
            Route::get('church-bible-reading', BibleReading::class)->name('churches.bible-reading');
            Route::get('church-good-practices', ChuchGoodPractices::class)->name('churches.good-pratices');
            Route::get('church-pastoral', OnlyChurches::class)->name('churches.pastoral');
            Route::get('church-members', Members::class)->name('churches.members');
            Route::get('church-ministries', Ministries::class)->name('churches.ministries');
            Route::get('church-events', Events::class)->name('churches.events');
            Route::get('church-schedules', Schedules::class)->name('churches.schedules');
            Route::get('church-scale', Scale::class)->name('churches.scale');
            Route::get('church-standard-cult', StandardCult::class)->name('churches.standard-cult');
            Route::get('church-talent-map', TalentMap::class)->name('churches.talent-map');
            Route::get('church-calendar', Calendar::class)->name('churches.calendar');

            Route::get('church-member-cards/{cartaoId?}', MemberCards::class)->name('member-cards');
            Route::get('church-member-migration', \App\Livewire\Church\Members\MemberMigration::class)->name('member-migration');

            Route::get('alliance-church', AllianceChurch::class)->name('alliance.tools');
            Route::get('alliance-church/my-alliance', MyAlliance::class)->name('alliance.my');
            Route::get('alliance-church/community/{aliancaId?}', Community::class)->name('community');
            Route::get('alliance-church/community-church', Community::class)->name('community-nav');

            //* CHURCHES CHAT
            Route::get('chat-churches', ChurchChat::class)->name('chat.churches');
            Route::get('chat/church', ChurchChat::class)->name('chat-nav');
            Route::get('private-chat', PrivateChat::class)->name('chat.private-chat');
            Route::get('chat/private', PrivateChat::class)->name('private-chat-nav');

            
            //* COURSE OF CHURCH
            Route::prefix('church-courses')->name('courses.')->group(function(){
                Route::get('courses', Courses::class)->name('courses');
                Route::get('registration', CourseRegistered::class)->name('registration');
                Route::get('class', CourseClass::class)->name('class');
                Route::get('certificates', Certificates::class)->name('certificates');
            });

             //* SPECIAL REQUEST AND STATISTICS OF CHURCH
             Route::get('church-special-requests', SpecialRequests::class)->name('special-requests');
             Route::get('church-reports', Reports::class)->name('reports');
             Route::get('church-statistics', Statistics::class)->name('statistics');

          
            //* MARCKETPLACE OF CHURCH
            Route::prefix('church-marketplace')->name('marketplace.')->group(function(){
                Route::get('products', Products::class)->name('products');
                Route::get('orders', Orders::class)->name('orders');
                Route::get('payments', Payments::class)->name('payment');
            });

            Route::get('leadership-body', LeadershipBody::class)->name('leadership-body');
            Route::get('rbac-control', \App\Livewire\RbacControl\RBACControl::class)->name('rbac-control');
            Route::get('only-posts', Posts::class)->name('only-posts');
            Route::get('church-showcase', ChurchShowcase::class)->name('churches.showcase');
            Route::get('church-volunteers', Volunteers::class)->name('churches.volunteers');
            Route::get('church-resources', Resources::class)->name('churches.resources');
            Route::get('church-pastoral-care', PastoralCare::class)->name('churches.pastoral-care');




             //* FINANCIAL CHURCH
            Route::prefix('church-financial')->name('financial.')->group(function(){
                Route::get('moviment', FinancialMoviment::class)->name('moviment');
                Route::get('report', FinancialReport::class)->name('report');
                Route::get('accounts', FinancialAccount::class)->name('accounts');
                Route::get('online-donations', OnlineDonations::class)->name('churches.online-donations');
            });

             //* ENGAGEMENT OF CHURCH
            Route::prefix('church-engagement')->name('engagement.')->group(function() {
                Route::get('badges', Badges::class)->name('badges');
                Route::get('points', Points::class)->name('points');
                Route::get('polls', Polls::class)->name('polls');
            });

               //* BILLINGS OF CHURCH
            Route::get('church-subscription', Subscription::class)->name('subscription');

        });

     


    });



     Route::get('/user/two-factor-authentication', TwoFactorPage::class)->name('two-factor.show');


    });

});



//** GLOBAL FALLBACK ROUTE - Redirect to e-commerce home for any non-existent route */
Route::fallback(function () {
    return redirect('/e-commerce');
});

