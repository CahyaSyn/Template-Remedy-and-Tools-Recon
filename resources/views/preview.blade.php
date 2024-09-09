<style>
    /* responsive table preview with scroll x */
    .tb_preview {
        width: 100%;
        overflow-x: auto;
        overflow-y: auto;
        display: block;
        white-space: nowrap;
        height: 500px;
    }
</style>
<section class="content py-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Preview Updated Data</h3>
                        <table class="table tb_preview">
                            @foreach ($data as $row)
                                <tr>
                                    @foreach ($row as $cell)
                                        <td>{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </table>
                        <form action="{{ route('tools.download') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <button type="submit">Download Updated File</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
