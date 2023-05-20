@extends('layouts.admin')
@section('pageTitle', 'Produk')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
@endsection

@section('content')
<div id="controller">
  <div class="card card-primary card-outline">
    <div class="card-header">
      <h3 class="card-title">
        <form :action="urlCart" method="POST" @submit="storeCart()">
          @csrf
          <div class="form-group">
            <div class="row">
              <div class="col-4">
                <input type="text" name="search" v-model="search" class="form-control" placeholder="Cari Produk....">
              </div>
              <div class="col-4">
                <input type="hidden" name="product_id" :value="product.id" required>
                <input type="text" name="name" class="form-control" :value="product.name" disabled>
              </div>
              <div class="col-1">
                <input type="text" name="qty" class="form-control" placeholder="Qty" required>
              </div>
              <div class="col-3">
                <button type="submit" class="btn btn-primary">Tambah Barang</button>
              </div>
            </div>
          </div>
        </form>
      </h3>
    </div>
    <div class="card-body">
      <table class="table table-bordered table-sm" id="dataCart">
        <thead>
          <tr>
            <th class="text-center">Nama</th>
            <th class="text-center">Harga</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Total</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
      </table>
      <br>
      <div class="form-group">
        <div class="row">
          <div class="col-10"></div>
          <div class="col-2">
            Total Harga Rp. @{{ total }}
          </div>
        </div>
        <div class="row">
          <div class="col-10"></div>
          <div class="col-2">
            <a href="{{ url('checkout') }}" class="btn btn-primary">Checkout</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="edit-qty-form">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Jumlah Pembelian</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form :action="cartUrl" method="POST" @submit="storeCart($event, cart.id)">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <div class="form-group">
              <div class="row">
                <div class="col-7">
                  <input type="text" class="form-control" :value="cart.name" disabled>
                </div>
                <div class="col-2">
                  <input type="number" name="qty" class="form-control" placeholder="Qty" :value="cart.qty" required>
                </div>
                <div class="col-3">
                  <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
              </div>
            </div>
          </form>
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
  var urlCart = '{{ url('api/cart') }}';
  var urlProducts = '{{ url('api/products?cart=1') }}';
  var search='';

  var cartColumn = [
    {data:'name', class:'text-center', orderable:true},
    {data:'price', class:'text-center', orderable:true},
    {data:'qty', class:'text-center', width:'50px', orderable:true},
    {data:'total', class:'text-center', orderable:true},
    {render: function(index, row, data, meta){
      return `
        <a class="btn btn-sm btn-warning" title="Edit Qty" onclick="controller.editQty(${meta.row})"><i class="fas fa-pen" style="color:#fff"></i></a>
        <a class="btn btn-sm btn-danger" title="Hapus Barang" onclick="controller.deleteProduct(${data.id})"><i class="fas fa-trash"></i></a>
      `;
    }, class: 'text-center', width: '100px', orderable: false}
  ];

  var productColumn = [
    {data:'name', class:'text-center', orderable:true},
    {data:'price', class:'text-center', orderable:true},
    {data:'stock', class:'text-center', orderable:true},
    {render: function(index, row, data, meta){
      return `
        <a class="btn btn-sm btn-warning" title="Edit Qty" onclick="controller.addProduct(${meta.row})"><i class="fas fa-plus" style="color:#fff"></i></a>
      `;
    }, class: 'text-center', width: '100px', orderable: false}
  ];

  var controller = new Vue({
    el: '#controller',
    data: {
      carts: [],
      cart: {},
      urlCart,
      product: {},
      urlProducts,
      editStatus:false,
      search:'',
      total:0
    },
    mounted: function(){
      this.getCart();
    },
    methods:{
      getCart(){
        const _this = this;
        _this.table = $('#dataCart').DataTable({
          ajax: {
            url: _this.urlCart,
            type: 'GET',
          },
          columns: cartColumn,
          lengthChange: false
        }).on('xhr', function(){
          _this.carts = _this.table.ajax.json().data;
          // console.log(_this.table.ajax.json().datatables.original.data);
          _this.sumTransaction();
          console.log(_this.total);
        });
      },
      sumTransaction(){
        const _this = this;
        $.ajax({
          url: 'api/sum-transaction',
          method: 'GET',
          success: function(data){
            _this.total = data;
          }
        })
      },
      getProduct(){
        if(this.search == ''){
          this.product = {};
        }else{
          const _this = this;
          _this.data = $.ajax({
          url: urlProducts+'?&search='+this.search,
          method: 'GET',
          success: function(data){
            _this.product = JSON.parse(data)[0];
          },
          error: function(error){
            console.log(this.search);
          },
        });
        }
      },
      storeCart(event, id){
        event.preventDefault();
        this.urlCart = this.editStatus ? this.urlCart+'/'+id : this.urlCart;
        axios.post(this.urlCart, new FormData($(event.target)[0])).then(response => {
          $('#edit-qty-form').modal('hide');
          this.table.ajax.reload();
        })
        this.editStatus = false;
      },
      editQty(row){
        this.editStatus = true;
        this.cart = this.carts[row];
        console.log(this.cart);
        $('#edit-qty-form').modal();
      },
      updateQty(event, id){
        event.preventDefault();
        axios.post(urlCart+'/'+id, new FormData($(event.target)[0])).then(response => {
          $('#edit-qty-form').modal('hide');
          this.table.ajax.reload();
        })
      },
      deleteProduct(id){
        axios.post(this.urlCart+'/'+id, {_method: 'DELETE'}).then(response => {
          alert('Data has been removed');
        });
        this.table.ajax.reload();
      },
    },
    computed: {
      totalPrice() {
        return this.cartColumn.reduce((acc, item) => acc + item.value, 0);
      }
    }
  });
</script>
<script type="text/javascript">
  $('input[name=search]').on('input', function(){
    this.search = $('input[name=search]').val();
    controller.getProduct();
  });
</script>
@endsection