<?php 
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="data/css/style.css">
    <script src="data/js/script.js" defer></script>
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Home</a>
            <a href="history.php">History</a>
            <a href="mutation.php">Mutation</a>
            <a href="setting.php">Settings</a>
        </nav>
    </header>
    <main>
        <section class="hero">
            <h1 id="header1">DASHBOARD</h1>
            <div class="add-item">
                <button id="btn" onclick="openPopup()">Add Item</button>
            </div>
            <!-- POP UP -->
            <div class="overlay" id="popupOverlay">
                <div class="popup">
                <span class="close" onclick="closePopup()">&times;</span>
                <h2>Tambah Item</h2>
                <form action="tambah.php" method="POST">
                    <label>Customer:</label>
                    <input type="text" name="customer" placeholder="Masukkan nama" required>

                    <label>Spx:</label>
                    <input type="number" name="spx" placeholder="Masukkan jumlah">

                    <label>Anter:</label>
                    <input type="number" name="anter" placeholder="Masukkan jumlah">

                    <label>SiCepat:</label>
                    <input type="number" name="sicepat" placeholder="Masukkan jumlah">

                    <label>J&T:</label>
                    <input type="number" name="jt" placeholder="Masukkan jumlah">

                    <label>JNE:</label>
                    <input type="number" name="jne" placeholder="Masukkan jumlah">

                    <label>JNT Cargo:</label>
                    <input type="number" name="jntcargo" placeholder="Masukkan jumlah">

                    <label>JNE Cargo:</label>
                    <input type="number" name="jnecargo" placeholder="Masukkan jumlah">

                    <label>Pos:</label>
                    <input type="number" name="pos" placeholder="Masukkan jumlah">

                    <button type="submit">Simpan</button>
                </form>
                </div>
            </div>

            </div>
            <!-- Empty -->
            <section>
                <h2>Data Pengiriman</h2>
                <p id="date">Date: <?php echo date("Y-m-d"); ?></p>
                <table border="1" cellpadding="5" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Customer</th>
                            <th>Spx</th>
                            <th>Anter</th>
                            <th>Sicepat</th>
                            <th>J&T</th>
                            <th>JNE</th>
                            <th>JNT Cargo</th>
                            <th>JNE Cargo</th>
                            <th>Pos</th>
                            <th>Total</th>
                            <th>Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query ambil data
                        $sql = "SELECT s.id, c.customer_name, 
                                s.spx, s.anter, s.sicepat, 
                                s.jnt, s.jne, s.jnt_cargo, 
                                s.jne_cargo, s.pos, s.total
                                FROM shipments s 
                                JOIN customers c ON s.customer_id = c.id
                                WHERE s.shipment_date = CURDATE()";

                        $result = $conn->query($sql);

                        // cek error query
                        if (!$result) {
                            die("Query error: " . $conn->error);
                        }

                        $no = 1;
                        $totals = [
                            "spx"=>0, "anter"=>0, "sicepat"=>0, "jnt"=>0, "jne"=>0,
                            "jnt_cargo"=>0, "jne_cargo"=>0, "pos"=>0, "total"=>0
                        ];

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()){
                                echo "<tr>
                                    <td>".$no++."</td>
                                    <td>".$row['customer_name']."</td>
                                    <td>".$row['spx']."</td>
                                    <td>".$row['anter']."</td>
                                    <td>".$row['sicepat']."</td>
                                    <td>".$row['jnt']."</td>
                                    <td>".$row['jne']."</td>
                                    <td>".$row['jnt_cargo']."</td>
                                    <td>".$row['jne_cargo']."</td>
                                    <td>".$row['pos']."</td>
                                    <td>".$row['total']."</td>
                                    <td>
                                        <a href='edit.php?id=".$row['id']."'>Edit</a> | 
                                        <a href='delete.php?id=".$row['id']."' onclick=\"return confirm('Yakin mau hapus data ini?');\">Delete</a>

                                    </td>
                                </tr>";

                                // hitung total
                                $totals['spx']       += $row['spx'];
                                $totals['anter']     += $row['anter'];
                                $totals['sicepat']   += $row['sicepat'];
                                $totals['jnt']       += $row['jnt'];
                                $totals['jne']       += $row['jne'];
                                $totals['jnt_cargo'] += $row['jnt_cargo'];
                                $totals['jne_cargo'] += $row['jne_cargo'];
                                $totals['pos']       += $row['pos'];
                                $totals['total']     += $row['total'];
                            }
                        } else {
                            echo "<tr><td colspan='12' align='center'>Belum ada data</td></tr>";
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2"><strong>Total</strong></td>
                            <td><?php echo $totals['spx']; ?></td>
                            <td><?php echo $totals['anter']; ?></td>
                            <td><?php echo $totals['sicepat']; ?></td>
                            <td><?php echo $totals['jnt']; ?></td>
                            <td><?php echo $totals['jne']; ?></td>
                            <td><?php echo $totals['jnt_cargo']; ?></td>
                            <td><?php echo $totals['jne_cargo']; ?></td>
                            <td><?php echo $totals['pos']; ?></td>
                            <td><?php echo $totals['total']; ?></td>
                            <td>-</td>
                        </tr>
                    </tfoot>
                </table>
            </section>
        </section>
    </main>
</body>
</html>