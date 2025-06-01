<?php
include "../includes/header.php";
include '../includes/config.php';

$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$cartItems = [];

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    try {
        $stmt = mysqli_prepare($conn, "
            SELECT 
                b.booking_id AS item_id,
                u.name AS username,
                t.title,
                b.duration,
                b.people,
                b.price AS total,
                b.status,
                b.qr_code_image,
                g.name AS guide_name
            FROM bookings b
            JOIN users u ON b.user_id = u.user_id
            JOIN tours t ON b.tour_id = t.tour_id
            LEFT JOIN guides g ON b.guide_id = g.guide_id
            WHERE b.user_id = ?
            ORDER BY b.booking_id DESC
        ");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $cartItems[] = $row;
        }
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        echo "<p class='text-danger text-center mt-5'>Error loading cart: " . htmlspecialchars($e->getMessage()) . "</p>";
        exit;
    }
} else {
    echo "<p class='text-danger text-center mt-5'>You must be logged in to view your cart.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tour Booking Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Roboto', sans-serif;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            padding: 20px;
        }

        .card-header {
            font-size: 1.5rem;
            font-weight: 600;
            background-color: #f8f9fa;
        }

        .table-scrollable {
            overflow-x: auto;
        }

        .btn-group .btn {
            margin-right: 5px;
        }

        .table th,
        .table td {
            vertical-align: middle !important;
            white-space: nowrap;
        }

        .action-btns button {
            margin: 0 2px;
        }

        .modal-body img {
            max-width: 200px;
            border: 1px solid #ccc;
            border-radius: 12px;
            padding: 5px;
        }

        .btn-info {
            background-color: #4f8fd1;
            border-color: #4f8fd1;
        }

        .btn-info:hover {
            background-color: #3d74a2;
            border-color: #3d74a2;
        }

        .table-striped tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .table-striped tbody tr:nth-child(even) {
            background-color: #ffffff;
        }

        .footer {
            font-size: 14px;
            color: #777;
            text-align: center;
            margin-top: 30px;
        }

        .modal-body {
            font-size: 1.1rem;
            text-align: center;
        }

        .modal-body img {
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .total {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
        }

        .modal-header {
            background-color: #f1f1f1;
        }

        .modal-title {
            font-size: 1.25rem;
        }

        .modal {
            position: fixed;
            z-index: 99999;
            margin-bottom: -20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tour Booking Cart</h5>
                <div class="btn-group">
                    <button onclick="filterTable()" class="btn btn-outline-primary"><i class="fas fa-filter"></i>
                        Filter</button>
                    <button onclick="clearCart()" class="btn btn-outline-danger"><i class="fas fa-trash"></i>
                        Clear</button>
                    <button onclick="exportExcel()" class="btn btn-outline-success"><i class="fas fa-file-csv"></i>
                        Export to Excel</button>
                    <button onclick="printTableOnly()" class="btn btn-outline-info"><i class="fas fa-print"></i>
                        Print</button>
                </div>
            </div>

            <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search..."
                class="form-control mb-3">

            <div class="table-scrollable">
                <table class="table table-bordered table-striped text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Money Transfer</th>
                            <th>Username</th>
                            <th>Tour Title</th>
                            <th>Duration</th>
                            <th>People</th>
                            <th>Guide</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>QR Code</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="cartTableBody">
                        <?php
                        if (!empty($cartItems)) {
                            foreach ($cartItems as $index => $item) {
                                echo "<tr data-item-id='{$item['item_id']}'>
                                    <td>" . ($index + 1) . "</td>
                                    <td><button class='btn btn-sm btn-outline-info' onclick='showTransferModal($index)'>Transfer</button></td>
                                    <td>" . htmlspecialchars($item['username']) . "</td>
                                    <td>" . htmlspecialchars($item['title']) . "</td>
                                    <td>{$item['duration']} day(s)</td>
                                    <td>{$item['people']}</td>
                                    <td>" . htmlspecialchars($item['guide_name'] ?? 'No Guide') . "</td>
                                    <td>\${" . number_format($item['total'], 2) . "}</td>
                                    <td>{$item['status']}</td>
                                    <td>";
                                if ($item['qr_code_image'] && file_exists("../Uploads/qr_codes/{$item['qr_code_image']}")) {
                                    echo "<a href='#' onclick='showQRModal(\"../Uploads/qr_codes/{$item['qr_code_image']}\")'>";
                                    echo "<img src='../Uploads/qr_codes/{$item['qr_code_image']}' alt='QR Code' style='max-width: 50px; cursor: pointer;'>";
                                    echo "</a>";
                                } else {
                                    echo "No QR Code";
                                }
                                echo "</td>
                                <td class='action-btns'>
                                    <button class='btn btn-sm btn-danger delete-btn' onclick='removeFromCart(" . htmlspecialchars($item['item_id'], ENT_QUOTES) . ")' data-id='" . htmlspecialchars($item['item_id'], ENT_QUOTES) . "'>
                                        <i class='fas fa-trash-alt'></i>
                                    </button>
                                </td>
                            </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='10' class='text-center'>No items in the cart.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Transfer Modal -->
    <div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="transferModalLabel">Money Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>KHQR:</strong></p>
                    <img src="../assets/images/myqr.jpg" alt="Payment QR Code" class="img-fluid center">
                    <p><strong>Username:</strong> <span id="username">USERNAME HERE</span></p>
                    <p><strong>Tour:</strong> <span id="modalTourTitle"></span></p>
                    <p><strong>Guide:</strong> <span id="modalGuideName"></span></p>
                    <p><strong>Total:</strong></p>
                    <input id="salaryField" class="form-control mb-3 text-center" readonly>
                    <p><strong>Upload Payment Proof:</strong></p>
                    <input type="file" id="qrCodeInput" accept="image/*" class="form-control mb-3">
                    <p><strong>Uploaded Proof:</strong></p>
                    <img id="qrCodePreview" src="" alt="Uploaded Proof" class="img-fluid center" style="display: none;">
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="transferConfirm">
                        <label class="form-check-label" for="transferConfirm">Money already transferred</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmTransfer()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Zoom Modal -->
    <div class="modal fade" id="qrCodeZoomModal" tabindex="-1" aria-labelledby="qrCodeZoomModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrCodeZoomModalLabel">QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="qr-code-container" style="position: relative; display: inline-block;">
                        <img id="qrCodeZoomImage" src="" alt="QR Code" class="img-fluid"
                            style="max-width: 300px; border-radius: 10px; transition: transform 0.3s ease;">
                    </div>
                    <div class="zoom-controls mt-3">
                        <button type="button" class="btn btn-outline-primary btn-sm me-2" onclick="zoomIn()">
                            <i class="fas fa-search-plus"></i> Zoom In
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm me-2" onclick="zoomOut()">
                            <i class="fas fa-search-minus"></i> Zoom Out
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetZoom()">
                            <i class="fas fa-sync-alt"></i> Reset
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .qr-code-container {
            overflow: hidden;
        }

        #qrCodeZoomImage {
            transform-origin: center center;
            will-change: transform;
            /* Improves performance for animations */
        }

        .zoom-controls {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
    </style>

    <div class="footer">
        <p>Thank you for booking with us!</p>
        <p>If you have any questions, feel free to contact our support.</p>
    </div>

    <script>
        function removeFromCart(itemId) {
            if (!confirm("Are you sure you want to remove this booking?")) {
                return;
            }

            const button = document.querySelector(`.delete-btn[data-id="${itemId}"]`);
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch('remove_cart_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin', // Ensure session cookies are sent
                body: JSON.stringify({ id: parseInt(itemId) })
            })
                .then(response => {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-trash-alt"></i>';
                    if (!response.ok) {
                        throw new Error(`Server error: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert("Booking removed successfully!");
                        const row = document.querySelector(`#cartTableBody tr[data-item-id="${itemId}"]`);
                        if (row) {
                            row.remove();
                            const rows = document.querySelectorAll("#cartTableBody tr");
                            if (rows.length === 0) {
                                document.querySelector("#cartTableBody").innerHTML = '<tr><td colspan="10" class="text-center">No items in the cart.</td></tr>';
                            } else {
                                rows.forEach((r, i) => {
                                    r.cells[0].innerText = i + 1;
                                });
                            }
                        }
                    } else {
                        if (data.message === 'User not logged in') {
                            alert("Session expired. Please log in again.");
                            window.location.href = 'login.php'; // Redirect to login page
                        } else {
                            alert("Error removing booking: " + (data.message || "Unknown error"));
                        }
                    }
                })
                .catch(error => {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-trash-alt"></i>';
                    console.error('Delete error:', error);
                    alert("Failed to remove booking: " + error.message);
                });
        }

        function searchTable() {
            const input = document.getElementById("searchInput").value.toLowerCase();
            const rows = document.querySelectorAll("#cartTableBody tr");
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
            });
        }

        function filterTable() {
            const filterBy = prompt("Filter by status (e.g., Pending, Confirmed) or guide name:");
            if (filterBy) {
                const rows = document.querySelectorAll("#cartTableBody tr");
                rows.forEach(row => {
                    const status = row.cells[8].innerText.toLowerCase();
                    const guide = row.cells[6].innerText.toLowerCase();
                    row.style.display = (status.includes(filterBy.toLowerCase()) || guide.includes(filterBy.toLowerCase())) ? "" : "none";
                });
            }
        }

        function exportExcel() {
            const table = document.getElementById("cartTableBody");
            const username = "<?php echo htmlspecialchars($_SESSION['name'] ?? 'Customer'); ?>";
            const date = new Date().toLocaleString();

            // Define data array for the worksheet
            const data = [
                ["Tour Booking Receipt"],
                ["Customer Name:", username],
                ["Date:", date],
                [],
                ["No.", "Title", "Duration", "People", "Guide", "Total (USD)"]
            ];

            let totalAmount = 0;
            let count = 1;

            // Extract data from table rows
            table.querySelectorAll('tr').forEach(row => {
                const cols = row.querySelectorAll('td');
                if (cols.length > 7) {
                    const title = cols[3].innerText.trim();
                    const duration = cols[4].innerText.trim();
                    const people = cols[5].innerText.trim();
                    const guide = cols[6].innerText.trim();
                    const total = parseFloat(cols[7].innerText.replace('$', '')) || 0;
                    totalAmount += total;

                    data.push([count, title, duration, parseInt(people), guide, total]);
                    count++;
                }
            });

            // Add total and footer
            data.push([], ["", "", "", "", "Total Amount", totalAmount]);
            data.push([], ["", "Thank you for your booking!"]);

            // Create worksheet
            const ws = XLSX.utils.aoa_to_sheet(data);

            // Apply formatting
            const range = XLSX.utils.decode_range(ws['!ref']);
            for (let row = range.s.r; row <= range.e.r; row++) {
                for (let col = range.s.c; col <= range.e.c; col++) {
                    const cellAddress = { r: row, c: col };
                    const cellRef = XLSX.utils.encode_cell(cellAddress);
                    if (!ws[cellRef]) continue;

                    // Default cell style
                    ws[cellRef].s = {
                        font: { name: 'Arial', sz: 12 },
                        alignment: { vertical: 'center', horizontal: 'left' },
                        border: {
                            top: { style: 'thin', color: { rgb: '000000' } },
                            bottom: { style: 'thin', color: { rgb: '000000' } },
                            left: { style: 'thin', color: { rgb: '000000' } },
                            right: { style: 'thin', color: { rgb: '000000' } }
                        }
                    };

                    // Specific formatting
                    if (row === 0) {
                        // Title row
                        ws[cellRef].s = {
                            font: { name: 'Arial', sz: 16, bold: true },
                            alignment: { vertical: 'center', horizontal: 'center' },
                            fill: { fgColor: { rgb: 'E6F3FF' } },
                            border: ws[cellRef].s.border
                        };
                    } else if (row === 1 || row === 2) {
                        // Customer Name and Date
                        ws[cellRef].s = {
                            font: { name: 'Arial', sz: 12, bold: col === 0 },
                            alignment: { vertical: 'center', horizontal: col === 0 ? 'right' : 'left' },
                            border: ws[cellRef].s.border
                        };
                    } else if (row === 4) {
                        // Header row
                        ws[cellRef].s = {
                            font: { name: 'Arial', sz: 12, bold: true },
                            alignment: { vertical: 'center', horizontal: 'center' },
                            fill: { fgColor: { rgb: 'D3D3D3' } },
                            border: ws[cellRef].s.border
                        };
                    } else if (row >= 5 && row < data.length - 2) {
                        // Data rows
                        if (col === 0 || col === 3) {
                            // No. and People: center, numeric
                            ws[cellRef].s.alignment.horizontal = 'center';
                        } else if (col === 5) {
                            // Total: right, currency format
                            ws[cellRef].s.alignment.horizontal = 'right';
                            ws[cellRef].z = '$#,##0.00';
                        }
                    } else if (row === data.length - 2) {
                        // Total Amount row
                        if (col === 4 || col === 5) {
                            ws[cellRef].s = {
                                font: { name: 'Arial', sz: 12, bold: true },
                                alignment: { vertical: 'center', horizontal: col === 4 ? 'right' : 'right' },
                                border: ws[cellRef].s.border
                            };
                            if (col === 5) ws[cellRef].z = '$#,##0.00';
                        }
                    } else if (row === data.length - 1) {
                        // Footer row
                        ws[cellRef].s = {
                            font: { name: 'Arial', sz: 10, italic: true },
                            alignment: { vertical: 'center', horizontal: 'center' },
                            border: ws[cellRef].s.border
                        };
                    }
                }
            }

            // Merge cells for title and footer
            ws['!merges'] = [
                { s: { r: 0, c: 0 }, e: { r: 0, c: 5 } }, // Merge title row
                { s: { r: data.length - 1, c: 0 }, e: { r: data.length - 1, c: 5 } } // Merge footer row
            ];

            // Set column widths
            ws['!cols'] = [
                { wch: 5 },  // No.
                { wch: 30 }, // Title
                { wch: 15 }, // Duration
                { wch: 10 }, // People
                { wch: 20 }, // Guide
                { wch: 15 }  // Total (USD)
            ];

            // Create workbook and append sheet
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Booking Receipt");

            // Export file
            XLSX.writeFile(wb, "tour_booking_receipt.xlsx", { bookType: 'xlsx', type: 'binary' });
        }

        function showTransferModal(index) {
            const modal = new bootstrap.Modal(document.getElementById('transferModal'));
            const row = document.querySelector(`#cartTableBody tr:nth-child(${index + 1})`);
            const username = row.querySelector('td:nth-child(3)').innerText;
            const tourTitle = row.querySelector('td:nth-child(4)').innerText;
            const guideName = row.querySelector('td:nth-child(7)').innerText;
            const total = row.querySelector('td:nth-child(8)').innerText.replace('$', '');
            const itemId = row.dataset.itemId;

            document.getElementById('username').innerText = username;
            document.getElementById('modalTourTitle').innerText = tourTitle;
            document.getElementById('modalGuideName').innerText = guideName;
            document.getElementById('salaryField').value = `$${total}`;
            document.getElementById('transferConfirm').checked = false;
            document.getElementById('qrCodeInput').value = '';
            document.getElementById('qrCodePreview').src = '';
            document.getElementById('qrCodePreview').style.display = 'none';
            document.getElementById('transferModal').dataset.itemId = itemId;
            modal.show();
        }

        document.getElementById('qrCodeInput').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const qrCodePreview = document.getElementById('qrCodePreview');
                    qrCodePreview.src = e.target.result;
                    qrCodePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                const qrCodePreview = document.getElementById('qrCodePreview');
                qrCodePreview.src = '';
                qrCodePreview.style.display = 'none';
            }
        });

        function confirmTransfer() {
            const checkbox = document.getElementById('transferConfirm');
            const qrCodeInput = document.getElementById('qrCodeInput');
            const itemId = document.getElementById('transferModal').dataset.itemId;

            if (!checkbox.checked) {
                alert("Please confirm that the money has been transferred.");
                return;
            }

            if (!qrCodeInput.files.length) {
                alert("Please upload a payment proof image.");
                return;
            }

            const formData = new FormData();
            formData.append('qrCode', qrCodeInput.files[0]);
            formData.append('booking_id', itemId);

            fetch('upload_qr_code.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Payment proof uploaded successfully! Booking is now pending admin confirmation.");
                        const modal = bootstrap.Modal.getInstance(document.getElementById('transferModal'));
                        modal.hide();
                        location.reload();
                    } else {
                        alert("Error uploading payment proof: " + data.message);
                    }
                })
                .catch(error => {
                    alert("Error: " + error);
                });
        }

        function clearCart() {
            if (confirm("Are you sure you want to clear your entire cart?")) {
                window.location.href = "clear_cart.php";
            }
        }

        function printTableOnly() {
            const table = document.getElementById("cartTableBody");
            const username = "<?php echo htmlspecialchars($_SESSION['name'] ?? 'Customer'); ?>";
            const date = new Date().toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' });

            let printWindow = window.open('', '', 'width=800,height=600');

            printWindow.document.write(`
        <html>
        <head>
            <title>Tour Booking Receipt</title>
            <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
            <style>
                body {
                    font-family: 'Open Sans', Arial, sans-serif;
                    margin: 20px;
                    color: #2d3748;
                    background-color: #f7fafc;
                    line-height: 1.6;
                }
                .container {
                    max-width: 800px;
                    margin: 0 auto;
                    background: #ffffff;
                    padding: 30px;
                    border-radius: 8px;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                }
                .header {
                    text-align: center;
                    padding-bottom: 20px;
                    border-bottom: 2px solid #e2e8f0;
                }
                .header h1 {
                    font-size: 28px;
                    font-weight: 600;
                    color: #1a202c;
                    margin: 0;
                }
                .header .logo {
                    width: 80px;
                    height: auto;
                    margin-bottom: 10px;
                }
                .info {
                    display: flex;
                    justify-content: space-between;
                    font-size: 14px;
                    color: #4a5568;
                    margin: 20px 0;
                }
                .info span {
                    font-weight: 600;
                }
                h3 {
                    font-size: 18px;
                    font-weight: 600;
                    color: #2d3748;
                    margin: 20px 0 10px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 14px;
                }
                th, td {
                    padding: 12px;
                    text-align: center;
                    border: 1px solid #e2e8f0;
                }
                th {
                    background-color: #4299e1;
                    color: #ffffff;
                    font-weight: 600;
                }
                td {
                    background-color: #ffffff;
                }
                tr:nth-child(even) td {
                    background-color: #edf2f7;
                }
                tr:hover td {
                    background-color: #bee3f8;
                }
                .currency {
                    text-align: right;
                }
                .total {
                    font-size: 16px;
                    font-weight: 600;
                    text-align: right;
                    margin-top: 20px;
                    color: #1a202c;
                }
                .footer {
                    text-align: center;
                    margin-top: 30px;
                    font-size: 12px;
                    color: #718096;
                    border-top: 1px solid #e2e8f0;
                    padding-top: 15px;
                }
                .footer p {
                    margin: 5px 0;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <img src="https://via.placeholder.com/80" alt="Logo" class="logo">
                    <h1>Tour Booking Receipt</h1>
                </div>
                <div class="info">
                    <div>Customer Name: <span>${username}</span></div>
                    <div>Date: <span>${date}</span></div>
                </div>
                <h3>Booking Details</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tour Title</th>
                            <th>Duration</th>
                            <th>People</th>
                            <th>Guide</th>
                            <th>Total (USD)</th>
                        </tr>
                    </thead>
                    <tbody>
    `);

            let totalAmount = 0;
            let index = 1;

            table.querySelectorAll('tr').forEach(row => {
                const cols = row.querySelectorAll('td');
                if (cols.length > 7) {
                    const title = cols[3].innerText.trim();
                    const duration = cols[4].innerText.trim();
                    const people = cols[5].innerText.trim();
                    const guide = cols[6].innerText.trim();
                    const total = parseFloat(cols[7].innerText.replace('$', '')) || 0;

                    totalAmount += total;

                    printWindow.document.write(`
                <tr>
                    <td>${index++}</td>
                    <td>${title}</td>
                    <td>${duration}</td>
                    <td>${people}</td>
                    <td>${guide}</td>
                    <td class="currency">$${total.toFixed(2)}</td>
                </tr>
            `);
                }
            });

            printWindow.document.write(`
                    </tbody>
                </table>
                <div class="total">Grand Total: $${totalAmount.toFixed(2)}</div>
                <div class="footer">
                    <p>Thank you for choosing our tours!</p>
                    <p>Contact us at support@tours.com | +1-800-123-4567</p>
                </div>
            </div>
        </body>
        </html>
    `);

            printWindow.document.close();
            printWindow.print();
        }
        let currentScale = 1;
        const scaleStep = 0.2; // Adjust zoom increment
        const minScale = 0.5; // Minimum zoom level
        const maxScale = 3; // Maximum zoom level

        function zoomIn() {
            if (currentScale < maxScale) {
                currentScale += scaleStep;
                updateImageScale();
            }
        }

        function zoomOut() {
            if (currentScale > minScale) {
                currentScale -= scaleStep;
                updateImageScale();
            }
        }

        function resetZoom() {
            currentScale = 1;
            updateImageScale();
        }

        function updateImageScale() {
            const qrImage = document.getElementById('qrCodeZoomImage');
            qrImage.style.transform = `scale(${currentScale})`;
        }

        function showQRModal(imageSrc) {
            const modal = new bootstrap.Modal(document.getElementById('qrCodeZoomModal'));
            const qrImage = document.getElementById('qrCodeZoomImage');
            qrImage.src = imageSrc;
            currentScale = 1; // Reset zoom when opening modal
            qrImage.style.transform = `scale(${currentScale})`;
            modal.show();
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include "./user_footer.php"; ?>
</body>

</html>