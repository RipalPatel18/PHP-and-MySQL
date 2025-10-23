<?php
require('includes/connect.php');

// Fetch all projects with client info
$sql_projects = "SELECT p.project_id, p.project_name, p.start_date, p.end_date, p.status, p.total_amount, c.name AS client_name
                 FROM project p
                 JOIN client c ON p.client_id = c.client_id
                 ORDER BY p.start_date ASC";
$result_projects = $conn->query($sql_projects);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Freelance Projects Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
  .hover-shadow:hover { box-shadow: 0 0 20px rgba(0,0,0,0.2); transition: 0.3s; }
</style>
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="text-center mb-5"><i class="bi bi-kanban-fill"></i> Freelance Projects Dashboard</h1>

<?php
if ($result_projects->num_rows > 0) {
    while($project = $result_projects->fetch_assoc()) {
        // Project status badge
        $statusBadge = '';
        switch(strtolower($project['status'])){
            case 'completed': $statusBadge = 'bg-success'; break;
            case 'ongoing': $statusBadge = 'bg-warning text-dark'; break;
            case 'cancelled': $statusBadge = 'bg-danger'; break;
        }

        echo '<div class="card mb-4 shadow-sm hover-shadow">';
        echo '<div class="card-header d-flex justify-content-between align-items-center">';
        echo '<strong><i class="bi bi-briefcase-fill"></i> Project #'.$project['project_id'].'</strong>';
        echo '<span class="badge '.$statusBadge.'">'.ucfirst($project['status']).'</span>';
        echo '</div>';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">'.$project['project_name'].'</h5>';
        echo '<p class="card-text"><i class="bi bi-person-fill"></i> <strong>Client:</strong> '.$project['client_name'].' | 
              <i class="bi bi-calendar2-event"></i> <strong>Start:</strong> '.$project['start_date'].' | 
              <strong>End:</strong> '.($project['end_date'] ?? 'N/A').' | 
              <strong>Total:</strong> $'.$project['total_amount'].'</p>';

        // Services
        $sql_services = "SELECT s.service_name, ps.quantity, ps.line_total
                         FROM project_services ps
                         JOIN services s ON ps.service_id = s.service_id
                         WHERE ps.project_id = ".$project['project_id'];
        $services_result = $conn->query($sql_services);

        if($services_result->num_rows > 0){
            echo '<button class="btn btn-outline-primary mb-2" data-bs-toggle="collapse" data-bs-target="#services'.$project['project_id'].'">
                    <i class="bi bi-card-checklist"></i> View Services
                  </button>';
            echo '<div class="collapse" id="services'.$project['project_id'].'">';
            echo '<table class="table table-striped mb-3">';
            echo '<thead><tr><th>Service</th><th>Quantity</th><th>Line Total</th></tr></thead><tbody>';
            while($service = $services_result->fetch_assoc()){
                echo '<tr><td>'.$service['service_name'].'</td><td>'.$service['quantity'].'</td><td>$'.$service['line_total'].'</td></tr>';
            }
            echo '</tbody></table></div>';
        }

        // Invoices
        $sql_invoices = "SELECT invoice_id, issue_date, due_date, amount, status 
                         FROM invoices 
                         WHERE project_id = ".$project['project_id'];
        $invoice_result = $conn->query($sql_invoices);

        if($invoice_result->num_rows > 0){
            echo '<button class="btn btn-outline-success mb-2" data-bs-toggle="collapse" data-bs-target="#invoices'.$project['project_id'].'">
                    <i class="bi bi-receipt"></i> View Invoices
                  </button>';
            echo '<div class="collapse" id="invoices'.$project['project_id'].'">';
            echo '<table class="table table-striped">';
            echo '<thead><tr><th>Invoice</th><th>Amount</th><th>Status</th></tr></thead><tbody>';
            while($invoice = $invoice_result->fetch_assoc()){
                // Invoice status badge
                $invoiceBadge = '';
                switch(strtolower($invoice['status'])){
                    case 'paid': $invoiceBadge = 'bg-success'; break;
                    case 'partial': $invoiceBadge = 'bg-warning text-dark'; break;
                    case 'unpaid': $invoiceBadge = 'bg-danger'; break;
                }

                echo '<tr><td>#'.$invoice['invoice_id'].'</td><td>$'.$invoice['amount'].'</td>
                      <td><span class="badge '.$invoiceBadge.'">'.ucfirst($invoice['status']).'</span></td></tr>';

                // Payments
                $sql_payments = "SELECT payment_date, payment_amount, payment_method, notes
                                 FROM payments 
                                 WHERE invoice_id = ".$invoice['invoice_id'];
                $payments_result = $conn->query($sql_payments);

                if($payments_result->num_rows > 0){
                    echo '<tr><td colspan="3"><strong>Payments:</strong><ul class="list-group list-group-flush">';
                    while($payment = $payments_result->fetch_assoc()){
                        echo '<li class="list-group-item p-1">Payment: $'.$payment['payment_amount'].' on '.$payment['payment_date'].' via '.$payment['payment_method'].' ('.$payment['notes'].')</li>';
                    }
                    echo '</ul></td></tr>';
                }
            }
            echo '</tbody></table></div>';
        }

        echo '</div></div>'; // card body + card
    }
} else {
    echo '<div class="alert alert-warning text-center">No projects found.</div>';
}

$conn->close();
?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
