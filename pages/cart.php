<?php
include "../includes/header.php";
include '../includes/config.php';
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$cartItems = [];
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = mysqli_prepare($conn, "
        SELECT 
            b.booking_id AS item_id,
            u.name AS username,
            t.title,
            b.duration,
            b.people,
            (b.people * b.price) AS total,
            b.status,
            b.qr_code_image
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN tours t ON b.tour_id = t.tour_id
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
                                    <td>\${$item['total']}</td>
                                    <td>{$item['status']}</td>
                                    <td>";
                                if ($item['qr_code_image'] && file_exists("../Uploads/qr_codes/{$item['qr_code_image']}")) {
                                    echo "<img src='../Uploads/qr_codes/{$item['qr_code_image']}' alt='QR Code' style='max-width: 50px;'>";
                                } else {
                                    echo "No QR Code";
                                }
                                echo "</td>
                                    <td class='action-btns'>
                                        <button class='btn btn-sm btn-danger' onclick='removeFromCart({$item['item_id']})'>
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
                    <p><strong>Net Salary:</strong></p>
                    <input id="salaryField" class="form-control mb-3 text-center" readonly>
                    <p><strong>Upload QR Code:</strong></p>
                    <input type="file" id="qrCodeInput" accept="image/*" class="form-control mb-3">
                    <p><strong>KHQR:</strong></p>
                    <img id="qrCodePreview" src="" alt="QR Code" class="img-fluid mb-2" style="display: none;">
                    <p class="fw-bold" id="username">USERNAME HERE</p>
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

    <div class="footer">
        <p>Thank you for booking with us!</p>
        <p>If you have any questions, feel free to contact our support.</p>
    </div>

    <script>
        function removeFromCart(itemId) {
            if (confirm("Are you sure you want to remove this booking?")) {
                fetch(`remove_cart_item.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: itemId })
                })
                    .then(response => {
                        // Check if response is OK and JSON
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        // Attempt to parse JSON
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                throw new Error(`Invalid JSON response: ${text.substring(0, 50)}...`);
                            }
                        });
                    })
                    .then(data => {
                        if (data.success) {
                            alert("Booking removed successfully!");
                            location.reload();
                        } else {
                            alert("Error removing booking: " + (data.message || "Unknown error"));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("Failed to remove booking: " + error.message);
                    });
            }
        }

        function searchTable() {
            const input = document.getElementById("searchInput").value.toLowerCase();
            const rows = document.querySelectorAll("#cartTableBody tr");
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
            });
        }

        function exportExcel() {
            const table = document.getElementById("cartTableBody");
            const username = "<?php echo $_SESSION['name'] ?? 'Customer'; ?>";
            const date = new Date().toLocaleString();
            const data = [
                ["Tour Booking Receipt"],
                ["Customer Name:", username],
                ["Date:", date],
                [],
                ["No.", "Title", "Duration", "People", "Total (USD)"]
            ];
            let totalAmount = 0;
            let count = 1;

            table.querySelectorAll('tr').forEach(row => {
                const cols = row.querySelectorAll('td');
                if (cols.length >= 4) {
                    const title = cols[3].innerText.trim();
                    const duration = cols[4].innerText.trim();
                    const people = cols[5].innerText.trim();
                    const total = parseFloat(cols[6].innerText.trim().replace('$', '')) || 0;
                    totalAmount += total;

                    data.push([count, title, duration, people, total.toFixed(2)]);
                    count++;
                }
            });

            data.push([], ["", "Total Amount", "", "", totalAmount.toFixed(2)]);
            data.push([], ["", "Thank you for your booking!"]);

            const ws = XLSX.utils.aoa_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Booking Receipt");
            XLSX.writeFile(wb, "tour_booking_receipt.xlsx");
        }

        function showTransferModal(index) {
            const modal = new bootstrap.Modal(document.getElementById('transferModal'));
            const username = document.querySelector(`#cartTableBody tr:nth-child(${index + 1}) td:nth-child(3)`).innerText;
            const total = document.querySelector(`#cartTableBody tr:nth-child(${index + 1}) td:nth-child(7)`).innerText.replace('$', '');
            const itemId = document.querySelector(`#cartTableBody tr:nth-child(${index + 1})`).dataset.itemId;

            document.getElementById('salaryField').value = `$${total}`;
            document.getElementById('username').innerText = username;
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
                alert("Please upload a QR code image.");
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
                        alert("QR code uploaded successfully! Booking is now pending admin confirmation.");
                        const modal = bootstrap.Modal.getInstance(document.getElementById('transferModal'));
                        modal.hide();
                        location.reload();
                    } else {
                        alert("Error uploading QR code: " + data.message);
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
            const username = "<?php echo $_SESSION['name'] ?? 'Customer'; ?>";
            const date = new Date().toLocaleString();

            let printWindow = window.open('', '', 'width=800,height=600');

            printWindow.document.write(`
                <html>
                <head>
                    <title>Tour Booking Receipt</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            padding: 20px;
                            margin: 0;
                            color: #333;
                        }
                        h2 {
                            text-align: center;
                            font-size: 24px;
                            margin-bottom: 20px;
                        }
                        .info {
                            margin-top: 10px;
                            font-size: 16px;
                            margin-bottom: 20px;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 20px;
                        }
                        th, td {
                            border: 1px solid #333;
                            padding: 10px;
                            text-align: center;
                            font-size: 14px;
                        }
                        th {
                            background-color: #f2f2f2;
                            font-weight: bold;
                        }
                        td {
                            background-color: #fafafa;
                        }
                        .total {
                            font-weight: bold;
                            font-size: 18px;
                            text-align: right;
                            margin-top: 20px;
                        }
                        .footer {
                            text-align: center;
                            margin-top: 40px;
                            font-size: 14px;
                            color: #777;
                        }
                        .footer p {
                            margin: 5px 0;
                        }
                    </style>
                </head>
                <body>
                    <h2>Tour Booking Receipt</h2>
                    <div class="info">Customer Name: <strong>${username}</strong></div>
                    <div class="info">Date: <strong>${date}</strong></div>
                    <h3>Booking Cart</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Username</th>
                                <th>Tour Title</th>
                                <th>Duration</th>
                                <th>People</th>
                                <th>Total (USD)</th>
                            </tr>
                        </thead>
                        <tbody>
            `);

            let totalAmount = 0;
            let index = 1;

            table.querySelectorAll("tr").forEach(row => {
                const cols = row.querySelectorAll("td");
                if (cols.length >= 4) {
                    const username = cols[2].innerText.trim();
                    const title = cols[3].innerText.trim();
                    const duration = cols[4].innerText.trim();
                    const people = cols[5].innerText.trim();
                    const total = parseFloat(cols[6].innerText.trim().replace('$', '').trim()) || 0;

                    totalAmount += total;

                    printWindow.document.write(`
                        <tr>
                            <td>${index++}</td>
                            <td>${username}</td>
                            <td>${title}</td>
                            <td>${duration}</td>
                            <td>${people}</td>
                            <td>$${total.toFixed(2)}</td>
                        </tr>
                    `);
                }
            });

            printWindow.document.write(`
                        </tbody>
                    </table>
                    <div class="total">Grand Total: $${totalAmount.toFixed(2)}</div>
                    <br>
                    <div class="footer">
                        <p>Thank you for your booking!</p>
                        <p>If you have any questions, please contact support@example.com</p>
                    </div>
                </body>
                </html>
            `);

            printWindow.document.close();
            printWindow.print();
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include "./user_footer.php"; ?>
</body>

</html>