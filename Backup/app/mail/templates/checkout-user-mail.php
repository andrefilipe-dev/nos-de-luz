<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Pagamento</title>
</head>

<body style="margin: 0; padding: 0; background-color: #05070a; font-family: Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color: #05070a; margin: 0; padding: 0;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="width: 100%; max-width: 600px; background-color: #11151a; border: 1px solid #ffffff1a; border-radius: 16px;">
                    <tr>
                        <td style="padding: 35px; color: #f0f3f6;">

                            <h1 style="margin: 0 0 25px 0; color: #ffffff; font-size: 28px;">
                                <span style="color: #198754;">All</span> Dyne Ltd
                            </h1>

                            <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #f0f3f6;">
                                Pagamento efetuado com sucesso!
                            </h3>

                            <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #f0f3f6;">
                                <?= htmlspecialchars($package_name) ?>
                            </h3>

                            <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #f0f3f6;">
                                R$ <?= htmlspecialchars(number_format($price, 2, ',', '.')) ?>
                            </h3>

                            <p style="margin: 0 0 25px 0; font-size: 15px; line-height: 1.6; color: #9ba5b0;">
                                <?= htmlspecialchars($description) ?>
                            </p>

                            <p>Muito obrigado por escolher a Nós de Luz. Você poderá conferir o andamento de seu
                                projeto pela página de projetos, se houver qualquer dúvida acione o suporte por meio de nosso
                                formulário de contato.</p>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="border-top: 1px solid #ffffff1a; padding-top: 30px;">
                                        <p style="margin: 0; font-size: 12px; line-height: 1.5; color: #9ba5b0;">
                                            Nós de Luz<br>
                                            Sempre digitalizando suas ideias.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>