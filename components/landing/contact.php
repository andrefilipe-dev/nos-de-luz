<?php
$alert = $_GET['alert'] ?? '';
?>

<section class="contact section-spacer" id="contact">
  <div class="custom-main-container">
    <div class="custom-text-container" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
      <span class="badge primary">Fale Conosco</span>
      <h1>Vamos conversar?</h1>
      <p>Tem alguma dúvida sobre nossos produtos ou pedidos? Fale com a gente!</p>
    </div>
    <div class="row g-3" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
      <div class="col-12 col-lg-6">
        <div class="card no-hover">
          <div class="card-body">
            <form class="w-100" action="<?= BASE_URL ?>app/controllers/contact-controller.php" method="POST">
              <div class="form-content mb-3">
                <div class="form-header">
                  <div class="icon-box"><i class="ri-chat-3-line"></i></div>
                  <h3 class="form-title">Entre em Contato</h3>
                </div>
                <div class="form-body row g-3">
                  <div class="form-field col-12">
                    <label for="name">Nome e Sobrenome</label>
                    <input class="form-control" name="name" id="name" placeholder="Ex: Rogério..." type="text"
                      minlength="3" maxlength="100" required />
                  </div>
                  <div class="form-field col-12">
                    <label for="email">E-mail</label>
                    <input class="form-control" name="email" id="email" placeholder="nome@exemplo.com" type="email"
                      minlength="5" maxlength="255" required />
                  </div>
                  <div class="form-field col-12">
                    <label for="subject">Assunto</label>
                    <input class="form-control" name="subject" id="subject" placeholder="Dúvida..." type="text"
                      minlength="3" maxlength="100" required />
                  </div>
                  <div class="form-field col-12">
                    <label for="description">Descrição</label>
                    <textarea class="form-control" name="description" id="description"
                      placeholder="Tenho uma dúvida sobre..." minlength="10" maxlength="1000" required></textarea>
                  </div>
                  <div class="col-12">
                    <div class="d-flex gap-3 justify-content-center">
                      <button type="submit" class="btn primary w-100">
                        Enviar Mensagem
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <span class="badge secondary red <?= ($alert === "email_error") ? "d-block" : "d-none" ?>">
                Erro ao enviar E-mail.
              </span>
              <span class="badge secondary green <?= ($alert === "email_sent") ? "d-block" : "d-none" ?>">
                E-mail enviado.
              </span>
            </form>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-6">
        <div class="row g-3">
          <div class="col-12 col-md-6">
            <div class="card no-hover h-100">
              <div class="card-body">
                <div class="icon-box">
                  <i class="ri-mail-line"></i>
                </div>
                <h3 class="card-title">E-mail</h3>
                <p>helenaandre2008@gmail.com</p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="card no-hover h-100">
              <div class="card-body">
                <div class="icon-box">
                  <i class="ri-instagram-line"></i>
                </div>
                <h3 class="card-title">Instagram</h3>
                <p>@hellenangel.macrame</p>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="card no-hover h-100">
              <div class="card-body">
                <div class="icon-box">
                  <i class="ri-whatsapp-line"></i>
                </div>
                <h3 class="card-title">Telefone</h3>
                <p>21 98625-4487</p>
              </div>
            </div>
          </div>
          <!---<div class="col-12 col-md-6">
            <div class="card no-hover h-100">
              <div class="card-body">
                <div class="icon-box">
                  <i class="ri-map-pin-line"></i>
                </div>
                <h3 class="card-title">Endereço</h3>
                <p>Rua Marechal Soares de Andréia, 90 - Realengo, Rio de Janeiro - RJ (CEP: 21710-180)</p>
              </div>
            </div>
          </div>-->
          <div class="col-12 col-md-6">
            <div class="card no-hover h-100">
              <div class="card-body">
                <div class="icon-box">
                  <i class="ri-customer-service-line"></i>
                </div>
                <h3 class="card-title">Atendimento</h3>
                <p>Atendimento das 10:00 às 20:00 de Segunda à Sexta.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>