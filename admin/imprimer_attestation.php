<?php

require_once '../classes/Attestation.php';
require_once '../classes/Employee.php';

if (isset($_GET['id'])) {
    $attestation = new Attestation();
    $employee = new Employee();

    $data = $attestation->getById($_GET['id']);
    $emp = $employee->getById($data['ID_EMP']);
    ?>
    <html>
    <head>
        <title>Attestation de Travail</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;700&family=Open+Sans:wght@400;600&display=swap');

            /* Reset */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
    font-family: 'Open Sans', sans-serif;
    background: #fff;
    padding: 30px 40px; 
    color: #333;
    line-height: 1.3; 
    max-width: 600px; 
    margin: auto;
    border: none;
    box-shadow: none;
}

h1 {
    font-size: 26px; 
    margin-bottom: 40px;
    border-bottom: 1.5px solid #2980b9;
    padding-bottom: 10px;
}

p {
    font-size: 14px; 
    margin-bottom: 15px;
}

ul {
    background-color: #f5f7fa;
    border: 1px solid #e1e4e8;
    border-radius: 6px;
    padding: 10px 15px;
    margin-bottom: 30px;
}

ul li {
    font-size: 14px;
    padding: 6px 10px;
    border-bottom: 1px solid #ddd;
}

ul li:last-child {
    border-bottom: none;
}

ul li strong {
    width: 150px;
    display: inline-block;
    color: #34495e;
}

.signature {
    margin-top: 50px;
    font-size: 16px;
    font-family: 'Cormorant Garamond', serif;
}

.signature p {
    width: 220px;
    margin: 20px auto 0 auto;
    border-top: 1.5px solid #2980b9;
    padding-top: 6px;
    font-style: italic;
    letter-spacing: 0.8px;
}
@media print {
  footer, .no-print {
    display: none !important; 
  }
}



        </style>
    </head>
    <body onload="window.print()">
        <img src="../img/evoltecbg.png" alt="Logo Evoltec">

        <h1>Attestation de Travail</h1>

        <p>Le soussigné, responsable des ressources humaines, atteste que :</p>

        <p><strong><?= htmlspecialchars($emp['NOM_EMP'] . ' ' . $emp['PRENOM_EMP']) ?></strong></p>

        <p>Embauché(e) depuis le <?= htmlspecialchars($emp['DATEEMBAUCH_EMP']) ?>, au poste de service <?= htmlspecialchars($emp['NOM_SERVICE']) ?>,</p>

        <p>Les informations concernant cet employé sont les suivantes :</p>
        <ul>
            <li><strong>Nom :</strong> <?= htmlspecialchars($emp['NOM_EMP']) ?></li>
            <li><strong>Prénom :</strong> <?= htmlspecialchars($emp['PRENOM_EMP']) ?></li>
            <li><strong>Date de naissance :</strong> <?= htmlspecialchars($emp['DATE_EMP']) ?></li>
            <li><strong>Téléphone :</strong> <?= htmlspecialchars($emp['TEL_EMP']) ?></li>
            <li><strong>Email :</strong> <?= htmlspecialchars($emp['EMAIL_EMP']) ?></li>
            <li><strong>Adresse :</strong> <?= htmlspecialchars($emp['ADRESSE_EMP']) ?></li>
            <li><strong>Date d'embauche :</strong> <?= htmlspecialchars($emp['DATEEMBAUCH_EMP']) ?></li>
            <li><strong>Nombre d'enfants :</strong> <?= htmlspecialchars($emp['NOMBRE_D_ENFANT']) ?></li>
        </ul>

        <p>A sollicité une attestation de travail pour le motif suivant :</p>

        <p>Fait le <?= htmlspecialchars($data['DATE_ATTESTATION']) ?></p>

        <div class="signature">
            <p>Signature : ______________________</p>
        </div>

    </body>
    </html>
<?php } ?>
