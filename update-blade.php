<?php
$content = file_get_contents('resources/views/admin/system-logs/index.blade.php');

$newHtml = <<<HTML
        <div class="table-responsive">
            <table id="dataTableExample" class="table log-table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>IP Address</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
HTML;

$content = preg_replace('/<div class="table-responsive">.*?<\/div>\s*<\/div>\s*<\/div>/s', $newHtml . "\n    </div>\n</div>", $content);

$newScript = <<<HTML
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof jQuery !== 'undefined' && $.fn.DataTable) {
            
            // Build the data table
            var table = $('#dataTableExample').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.system-logs.index') }}",
                    data: function (d) {
                        d.search = $('#search').val();
                        d.user_id = $('#user_id').val();
                        d.module = $('#module').val();
                        d.action_filter = $('#action').val();
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                    }
                },
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 25,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "language": {
                    "search": "Quick search logs:",
                    "searchPlaceholder": "Type to filter..."
                },
                "columnDefs": [
                    { "orderable": false, "targets": 5 } 
                ]
            });

            // Override form submission to reload table instead of full page turn
            $('form').on('submit', function(e) {
                e.preventDefault();
                table.draw();
            });
            
            // Allow clear button to reset UI and table
            $('.btn-secondary').on('click', function(e) {
                e.preventDefault();
                $('form')[0].reset();
                table.search('').draw();
            });
        }
    });
</script>
HTML;

$content = preg_replace('/<script>.*?(document\.addEventListener\("DOMContentLoaded".*?\}).*?<\/script>/s', $newScript, $content);

file_put_contents('resources/views/admin/system-logs/index.blade.php', $content);
