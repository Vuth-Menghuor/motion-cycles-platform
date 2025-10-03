<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DataTables from API</title>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body>

<table id="example" class="display" style="width:100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Descrption</th>
            <th>Price</th>
        </tr>
    </thead>
</table>

<script>
$(document).ready(function() {
    $('#example').DataTable({
        "ajax": {
            "url": "http://localhost:8100/api/products", // Replace with your API
            "dataSrc": "" // If your API returns an array directly
        },
        "columns": [
            { "data": "id" },
            { "data": "name" },
            { "data": "description" },
            { "data": "pricing" }
        ], 
        "pageLength": 2,         // default rows per page
        "lengthMenu": [5, 10, 25, 50], // dropdown options for rows per page
        "autoWidth": false,      // disables automatic column width calculation
        "scrollX": true,         // horizontal scroll if table width exceeds container
        "columnDefs": [
            { "width": "10%", "targets": 0 }, // ID column width
            { "width": "20%", "targets": 1 }, // Name column width
            { "width": "40%", "targets": 2 }, // Email column width
            { "width": "30%", "targets": 3 }
        ]

    });
});
</script>

</body>
</html>
