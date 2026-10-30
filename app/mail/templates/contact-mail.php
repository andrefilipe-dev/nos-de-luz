
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Projeto</title>
</head>

<body style="margin: 0; padding: 0; background-color: #fafafa; font-family: Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color: #fafafa; margin: 0; padding: 0;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="width: 100%; max-width: 600px; background-color: #fafafa; border: 1px solid #fafafa1a; border-radius: 16px; border: 1px solid #dddddd;">
                    <tr>
                        <td style="padding: 35px; color: #f0f3f6;">

                            <h1 style="margin: 0 0 25px 0; color: #f28c28; font-size: 28px;">
                                Nós de Luz
                            </h1>

                            <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #242424;">
                                Nova solicitação de <?= htmlspecialchars($name) ?>!
                            </h3>

                            <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #242424;">
                                <?= htmlspecialchars($subject) ?>
                            </h3>

                            <p style="margin: 0 0 25px 0; font-size: 15px; line-height: 1.6; color: #242424;">
                                <?= htmlspecialchars($description) ?>
                            </p>

                            <p style="margin: 0 0 25px 0; font-size: 15px; line-height: 1.6; color: #242424;">
                                E-mail para contato: <?= htmlspecialchars($email) ?>
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="border-top: 1px solid #fafafa1a; padding-top: 30px;">
                                        <p style="margin: 0; font-size: 12px; line-height: 1.5; color: #9ba5b0;">
                                            Nós de Luz<br>
                                            Tudo que você precisa em um só lugar
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