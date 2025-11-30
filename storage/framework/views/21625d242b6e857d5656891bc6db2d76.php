<div class="container-fluid py-5">
    <!-- Hero Section -->
    <div class="card bg-gradient-hero text-white border-0 shadow-lg mb-5">
        <div class="card-body p-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-envelope me-3"></i>
                        Entre em Contato
                    </h1>
                    <p class="lead mb-4">
                        Estamos aqui para ajudar! Entre em contato conosco para dúvidas, suporte ou parcerias.
                    </p>

                    <div class="row g-4">
                        <div class="col-auto text-center">
                            <div class="h4 mb-1"><i class="fas fa-clock"></i></div>
                            <div class="small opacity-75">24/7 Suporte</div>
                        </div>
                        <div class="col-auto text-center border-start border-white border-opacity-25 ps-4">
                            <div class="h4 mb-1"><i class="fas fa-reply"></i></div>
                            <div class="small opacity-75">Resposta Rápida</div>
                        </div>
                        <div class="col-auto text-center border-start border-white border-opacity-25 ps-4">
                            <div class="h4 mb-1"><i class="fas fa-heart"></i></div>
                            <div class="small opacity-75">Atendimento Personalizado</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 text-center d-none d-lg-block">
                    <i class="fas fa-comments hero-icon opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações de Contato e Formulário -->
    <div class="row mb-5">
        <!-- Informações de Contato -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4 p-md-5">
                    <h3 class="section-header text-primary fw-bold mb-4">
                        <i class="fas fa-info-circle me-2"></i> Informações
                    </h3>

                    <div class="contact-info">
                        <div class="contact-item mb-4">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                            </div>
                            <div class="contact-details">
                                <h6 class="fw-bold text-primary mb-1">Endereço</h6>
                                <p class="mb-0 small">Luanda, Angola<br>Centro Empresarial</p>
                            </div>
                        </div>

                        <div class="contact-item mb-4">
                            <div class="contact-icon">
                                <i class="fas fa-phone fa-2x text-success"></i>
                            </div>
                            <div class="contact-details">
                                <h6 class="fw-bold text-success mb-1">Telefone</h6>
                                <p class="mb-0 small">+244 923 456 789<br>+244 912 345 678</p>
                            </div>
                        </div>

                        <div class="contact-item mb-4">
                            <div class="contact-icon">
                                <i class="fas fa-envelope fa-2x text-warning"></i>
                            </div>
                            <div class="contact-details">
                                <h6 class="fw-bold text-warning mb-1">E-mail</h6>
                                <p class="mb-0 small">contato@omnigrejas.com<br>suporte@omnigrejas.com</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-clock fa-2x text-info"></i>
                            </div>
                            <div class="contact-details">
                                <h6 class="fw-bold text-info mb-1">Horário de Atendimento</h6>
                                <p class="mb-0 small">Segunda - Sexta: 8h às 18h<br>Sábado: 9h às 13h</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário de Contato -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <h3 class="section-header text-primary fw-bold mb-4">
                        <i class="fas fa-paper-plane me-2"></i> Envie sua Mensagem
                    </h3>

                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nome" class="form-label fw-semibold">Nome Completo</label>
                                <input type="text" class="form-control" id="nome" placeholder="Seu nome completo" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">E-mail</label>
                                <input type="email" class="form-control" id="email" placeholder="seu@email.com" required>
                            </div>

                            <div class="col-md-6">
                                <label for="telefone" class="form-label fw-semibold">Telefone</label>
                                <input type="tel" class="form-control" id="telefone" placeholder="+244 923 456 789">
                            </div>

                            <div class="col-md-6">
                                <label for="assunto" class="form-label fw-semibold">Assunto</label>
                                <select class="form-select" id="assunto" required>
                                    <option value="">Selecione um assunto</option>
                                    <option value="suporte">Suporte Técnico</option>
                                    <option value="vendas">Vendas e Planos</option>
                                    <option value="parceria">Parcerias</option>
                                    <option value="feedback">Feedback</option>
                                    <option value="outros">Outros</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="mensagem" class="form-label fw-semibold">Mensagem</label>
                                <textarea class="form-control" id="mensagem" rows="5" placeholder="Digite sua mensagem aqui..." required></textarea>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newsletter" checked>
                                    <label class="form-check-label small" for="newsletter">
                                        Quero receber novidades e atualizações da OMNIGREJAS
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-glow">
                                <i class="fas fa-paper-plane me-2"></i> Enviar Mensagem
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Rápido -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-5">
                <h2 class="section-header text-primary fw-bold display-5 mb-3">
                    <i class="fas fa-question-circle me-3"></i>Dúvidas Frequentes
                </h2>
                <p class="text-muted lead fs-5">Encontre respostas rápidas para suas dúvidas</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="faq-item">
                        <h5 class="fw-bold text-primary mb-2">
                            <i class="fas fa-clock me-2"></i>Qual o prazo de resposta?
                        </h5>
                        <p class="mb-0 small">Respondemos todas as mensagens em até 24 horas úteis.</p>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="faq-item">
                        <h5 class="fw-bold text-success mb-2">
                            <i class="fas fa-headset me-2"></i>Preciso de suporte urgente?
                        </h5>
                        <p class="mb-0 small">Para casos urgentes, ligue diretamente para nosso suporte.</p>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="faq-item">
                        <h5 class="fw-bold text-warning mb-2">
                            <i class="fas fa-building me-2"></i>Sou uma igreja, como começar?
                        </h5>
                        <p class="mb-0 small">Entre em contato conosco para uma demonstração personalizada.</p>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="faq-item">
                        <h5 class="fw-bold text-info mb-2">
                            <i class="fas fa-handshake me-2"></i>Quero ser parceiro?
                        </h5>
                        <p class="mb-0 small">Envie sua proposta através do formulário acima.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mapa ou Localização -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <h3 class="section-header text-primary fw-bold mb-3">
                    <i class="fas fa-map-marked-alt me-2"></i> Nossa Localização
                </h3>
                <p class="text-muted">Venha nos visitar em Luanda</p>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="map-placeholder bg-light rounded d-flex align-items-center justify-content-center" style="height: 300px;">
                        <div class="text-center">
                            <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Mapa Interativo</h5>
                            <p class="text-muted small">Luanda, Angola - Centro Empresarial</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="location-details">
                        <h5 class="fw-bold text-primary mb-3">Como Chegar</h5>

                        <div class="location-item mb-3">
                            <i class="fas fa-car text-success me-2"></i>
                            <span class="small">Estacionamento gratuito disponível</span>
                        </div>

                        <div class="location-item mb-3">
                            <i class="fas fa-bus text-info me-2"></i>
                            <span class="small">Próximo ao terminal de ônibus</span>
                        </div>

                        <div class="location-item">
                            <i class="fas fa-elevator text-warning me-2"></i>
                            <span class="small">Elevador disponível no prédio</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Final -->
    <div class="card cta-gradient text-white border-0 shadow-lg">
        <div class="card-body p-5 text-center">
            <h3 class="fw-bold display-5 mb-3">
                Pronto para Começar?
            </h3>

            <p class="lead mb-4 fs-5">
                Não perca tempo! Entre em contato agora e transforme a gestão da sua igreja.
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap mt-4">
                <a href="tel:+244923456789" class="btn btn-light btn-lg px-4 fw-bold shadow-sm">
                    <i class="fas fa-phone me-2"></i>Ligar Agora
                </a>
                <a href="mailto:contato@omnigrejas.com" class="btn btn-outline-light btn-lg px-4 fw-bold shadow-sm">
                    <i class="fas fa-envelope me-2"></i>Enviar E-mail
                </a>
                <a href="<?php echo e(route('ecommerce.subscription.upgrade')); ?>" class="btn btn-success btn-lg px-4 fw-bold shadow-sm">
                    <i class="fas fa-rocket me-2"></i>Ver Planos
                </a>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/ecommerce/contact.blade.php ENDPATH**/ ?>