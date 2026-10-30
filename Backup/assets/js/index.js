/* NAVBAR */
document.querySelectorAll('#top-navbar .navbar-nav > .nav-item > a:not(.dropdown-toggle)').forEach(link => {
  link.addEventListener('click', () => {
    if (window.innerWidth < 768) {
      const offcanvasElement = document.getElementById('top-navbar');
      const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasElement);
      offcanvas.hide();
    }
  });
});

/* FORMULÁRIO DE ETAPAS */
const btnPrev = document.querySelectorAll(".btnPrev");
const btnNext = document.querySelectorAll(".btnNext");
const formSteps = document.querySelectorAll(".step");

if (formSteps.length > 0) {
  let formStepsCounter = 0;

  function updateForm() {
    formSteps.forEach((step) => step.classList.remove("active"));
    formSteps[formStepsCounter].classList.add("active");
  }

  btnPrev.forEach((btn) => {
    btn.addEventListener("click", () => {
      if (formStepsCounter > 0) {
        formStepsCounter--;
        updateForm();
      }
    });
  });

  btnNext.forEach((btn) => {
    btn.addEventListener("click", () => {
      const currentStep = formSteps[formStepsCounter];
      const inputs = currentStep.querySelectorAll("input");

      for (const input of inputs) {
        if (!input.checkValidity()) {
          input.reportValidity();
          return; /*RESPONSÁVEL POR PARAR A FUNÇÃO*/
        }
      }

      if (formStepsCounter < formSteps.length - 1) {
        formStepsCounter++;
        updateForm();
      }
    });
  });

  updateForm();
}

/* BOTÃO DE MOSTRAR SENHA */
document.querySelectorAll('input[type="password"]').forEach(input => {
  const container = document.createElement('div');
  container.classList.add('password-field');

  input.parentNode.insertBefore(container, input);
  container.appendChild(input);

  const button = document.createElement('button');
  button.type = 'button';
  button.classList.add('password-toggle');
  button.setAttribute('aria-label', 'Mostrar senha');

  button.innerHTML = '<i class="ri-eye-line"></i>';

  container.appendChild(button);

  button.addEventListener('click', () => {

    const isPassword = input.type === 'password';

    input.type = isPassword ? 'text' : 'password';

    button.innerHTML = isPassword
      ? '<i class="ri-eye-off-line"></i>'
      : '<i class="ri-eye-line"></i>';

    button.setAttribute(
      'aria-label',
      isPassword ? 'Ocultar senha' : 'Mostrar senha'
    );
  });
});

/* FORMATAÇÕES JQUERY MASK PLUGIN */
$("#date").mask("00/00/0000");
$("#time").mask("00:00:00");
$("#cep").mask("00000-000");
$("#phone").mask("(00) 00000-0000");
$("#cpf").mask("000.000.000-00");
$("#card_number").mask("0000 0000 0000 0000");
$("#card_date").mask("00/00")

/* BIBLIOTECA AOS */
AOS.init({
  duration: 700,
  once: true,
  offset: 80
});