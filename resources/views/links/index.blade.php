<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css"
        integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js"
        integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

    <title>Document</title>
</head>

<body>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @enderror
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
        </div>
    @enderror

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div>
                Links
            </div>
            <div>
                <a href="{{ route('links.create') }}" class="btn btn-primary">Create Links</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table bordered-table" id="linksTable">
                <thead>
                    <tr>
                        <th>SR</th>
                        <th>User Name</th>
                        <th>Original Link</th>
                        <th>Short Link</th>
                        <th>Status</th>
                        <th>Visitors</th>
                        <th>Expire At</th>
                        <th>Created at</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#linksTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('links.index') }}",
                "columns": [{
                        data: "DT_RowIndex",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "user.name",

                    },
                    {
                        data: 'original_url',
                    },
                    {
                        data: 'short_link',
                    },
                    {
                        data: 'is_active',
                    },
                    {
                        data: 'analytics_count',

                    },
                    {
                        data: 'expire_at'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'action',
                    }
                ]
            })
        });
    </script>

</body>

</html>
