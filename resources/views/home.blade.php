@extends('layouts.admin')
@section('pageTitle', 'Dashboard')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
  <div id="controller">
    <div class="card card-primary card-outline">
      <div class="card-header">
        <h3 class="card-title">
          <a class="btn btn-sm btn-primary" href="{{ url('transaction') }}">Tambah Transaksi</a>
        </h3>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-sm" id="datatable">
          <thead>
            <tr>
              <th class="text-center">ID Transaksi</th>
              <th class="text-center">Tanggal Transaksi</th>
              <th class="text-center">Total Harga</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>

    <div class="modal fade" id="show-details">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Detail Transaksi</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered table-sm" id="table-detail">
              <thead>
                <tr>
                  <th class="text-center">Nama</th>
                  <th class="text-center">Harta</th>
                  <th class="text-center">Qty</th>
                  <th class="text-center">Total Harga</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('js')
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>

<script type="text/javascript">
  var urlTransactions = '{{ url('api/transactions') }}';
  
  var column = [
    {data:'id', class:'text-center', orderable:true},
    {data:'created_at', class:'text-center', orderable:true},
    {data:'total', class:'text-center', orderable:true},
    {render: function(index, row, data, meta){
      return `
        <a class="btn btn-sm btn-secondary" title="Lihat Detail" onclick="controller.showTransaction(${data.id})">Lihat Detail</a>
      `;
    }, class: 'text-center', width: '100px', orderable: false}
  ]

  var detailColumn = [
    {data:'name', class:'text-center', orderable:true},
    {data:'price', class:'text-center', orderable:true},
    {data:'qty', class:'text-center', orderable:true},
    {data:'total', class:'text-center', orderable:true}
  ]

  var controller = new Vue({
    el: '#controller',
    data: {
      transactions: [],
      transaction: {},
      urlTransactions,
    },
    mounted: function(){
      this.getTransactions();
    },
    methods:{
      getTransactions(){
        const _this = this;
        _this.table = $('#datatable').DataTable({
          ajax: {
            url: _this.urlTransactions,
            type: 'GET',
          },
          columns:column
        }).on('xhr', function(){
          _this.transactions = _this.table.ajax.json().data;
        });
      },
      showTransaction(id){
        const _this = this;
        _this.table = $('#table-detail').DataTable({
          ajax: {
            url: _this.urlTransactions+'/'+id,
            type: 'GET',
          },
          columns:detailColumn,
          bDestroy: true
        }).on('xhr', function(){
          _this.details = _this.table.ajax.json().data;
          console.log(_this.details);
        });
        $('#show-details').modal();
      }
    }
  });
</script>
@endsection