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
        <a class="btn btn-sm btn-primary" @click="createProduct()">Tambah Produk Baru</a>
        <a class="btn btn-sm btn-primary" @click="createCategory()">Tambah Kategori Baru</a>
      </h3>
    </div>
    <div class="card-body">
      <h5>Kategori</h5>
      <div class="row">
        <div class="col-5 col-sm-3">
          <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
            <div @click="requestProducts(0)" class="nav-link active" id="vert-tabs-home-tab" data-toggle="pill" href="#vert-tabs-home" role="tab" aria-controls="vert-tabs-home" aria-selected="true">
              <a>Semua Produk</a>
            </div>
            <div v-for="category in categories" @click="requestProducts(category.id)" class="nav-link" :href="category.name" data-toggle="pill" role="tab" aria-controls="vert-tabs-profile" aria-selected="false">
              <a>@{{ category.name }}</a>
              <div class="float-right">
                <a v-on:click="editCategory(category)" title="Ubah Nama" style="cursor: pointer"><i class="fas fa-pen" style="color: #f39c12"></i></a>
                <a @click="deleteCategory(category.id)" title="Hapus Kategori" style="cursor: pointer"><i class="fas fa-trash" style="color: #dc3545"></i></a>
              </div>
            </div>
            <div @click="requestProducts(null)" class="nav-link" id="vert-tabs-home-tab" data-toggle="pill" href="#vert-tabs-other" role="tab" aria-controls="vert-tabs-other" aria-selected="false">
              <a>Tanpa Kategori</a>
            </div>
          </div>
        </div>
        <div class="col-7 col-sm-9">
          <div class="tab-content" id="vert-tabs-tabContent">
            <table class="table table-bordered table-sm" id="datatable">
              <thead>
                <tr>
                  <th class="text-center">Nama</th>
                  <th class="text-center">Stok</th>
                  <th class="text-center">Harga</th>
                  <th class="text-center">Kategori</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="product-form">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" v-if="!editStatus">Tambah Produk</h4>
          <h4 class="modal-title" v-if="editStatus">Ubah Detail Produk</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form :action="urlProducts" method="POST" @submit="storeProduct($event, product.id)">
          @csrf
          <input type="hidden" v-if="editStatus" name="_method" value="PUT">
          <div class="modal-body">
            <div class="form-group">
              <div class="row">
                <div class="col-12">
                  <label>Nama</label>
                  <input type="text" name="name" class="form-control" placeholder="Nama Produk" :value="product.name" required>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="row">
                <div class="col-6">
                  <label>Kategori</label>
                  <select name="category_id" class="form-control">
                    <option v-for="category in categories" :value="category.id">@{{ category.name }}</option>
                  </select>
                </div>
                <div class="col-2" v-if="!editStatus">
                  <label>Stok</label>
                  <input type="number" name="stock" class="form-control" placeholder="Stock" :value="product.stock" required>
                </div>
                <div class="col-4">
                  <label>Harga</label>
                  <input type="number" name="price" class="form-control" placeholder="Harga" :value="product.price" required>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="category-form">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" v-if="!editStatus">Tambah Kategori</h4>
          <h4 class="modal-title" v-if="editStatus">Ubah Nama Kategori</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form :action="urlCategories" method="POST" @submit="storeCategory($event, category.id)">
          @csrf
          <input type="hidden" v-if="editStatus" name="_method" value="PUT">
          <div class="modal-body">
            <div class="form-group">
              <div class="row">
                <div class="col-12">
                  <label>Nama</label>
                  <input type="text" name="name" class="form-control" :value="category.name" placeholder="Nama Kategori" required>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Simpan Kategori</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="restock-form">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Restock Produk</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form :action="urlProducts" method="POST" @submit="storeProduct($event, product.id)">
          @csrf
          @method('patch')
          <div class="modal-body">
            <div class="form-group">
              <div class="row">
                <div class="col-8">
                  <label>Nama</label>
                  <input type="text" class="form-control" :value="product.name" disabled>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="row">
                <div class="col-2">
                  <label>Stok Tersedia</label>
                  <input type="number" class="form-control" placeholder="Stock" :value="product.stock" disabled>
                </div>
                <div class="col-2">
                  <label>Stok Masuk</label>
                  <input type="number" class="form-control" name="stock" placeholder="Stok Masuk" required>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
          </div>
        </form>
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
  var urlCategories = '{{ url('api/categories') }}';
  var urlProducts = '{{ url('api/products') }}';

  var columns = [
    {data:'name', class:'text-center', orderable:true},
    {data:'stock', class:'text-center', width:'50px', orderable:true},
    {data:'price', class:'text-center', orderable:true},
    {data:'category_name', class:'text-center', orderable:true},
    {render: function(index, row, data, meta){
      return `
        <a class="btn btn-sm btn-secondary" title="Tambah Stok" onclick="controller.restockProduct(${meta.row})"><i class="fas fa-plus"></i></a>
        <a class="btn btn-sm btn-warning" title="Edit Data" onclick="controller.editProduct(${meta.row})"><i class="fas fa-pen" style="color:#fff"></i></a>
        <a class="btn btn-sm btn-danger" title="Hapus Produk" onclick="controller.deleteProduct(event, ${data.id})"><i class="fas fa-trash"></i></a>
      `;
    }, class: 'text-center', width: '100px', orderable: false}
  ]

  var controller = new Vue({
    el: '#controller',
    data: {
      products: [],
      product: {},
      urlProducts,
      urlCategories,
      categories: [],
      category: {},
      editStatus:false,
    },
    mounted: function(){
      this.getProducts();
      this.getCategories();
    },
    methods:{
      getProducts(){
        const _this = this;
        _this.table = $('#datatable').DataTable({
          ajax: {
            url: _this.urlProducts,
            type: 'GET',
          },
          columns
        }).on('xhr', function(){
          _this.products = _this.table.ajax.json().data;
        });
      },
      createProduct(){
        this.product = {};
        this.editStatus = false;
        $('#product-form').modal();
      },
      editProduct(row){
        this.product = this.products[row];
        this.editStatus = true;
        $('#product-form').modal();
      },
      storeProduct(event, id){
        event.preventDefault();
        const _this = this;
        var urlProduct = this.editStatus ? this.urlProducts+'/'+id : this.urlProducts;
        axios.post(urlProduct, new FormData($(event.target)[0])).then(response => {
          $('#product-form').modal('hide');
          $('#restock-form').modal('hide');
          _this.table.ajax.reload();
        })
      },
      deleteProduct(event, id){
        if(confirm("Are you sure?")){
          // $(event.target).parents('tr').remove();
          axios.post(this.urlProducts+'/'+id, {_method: 'DELETE'}).then(response => {
            alert('Data has been removed');
          });
          this.table.ajax.reload();
        }
      },
      restockProduct(row){
        this.editStatus = true;
        this.product = this.products[row];
        $('#restock-form').modal();
      },
      storeRestock(event, id){
        event.preventDefault();
        const _this = this;
        var urlRestock = this.urlProducts+'/'+id+'/restock';
        axios.post(urlRestock, new FormData($(event.target)[0])).then(response => {
          alert('Sukses');
        });
        this.table.ajax.reload();
      },
      requestProducts(id){
        if(id == 0){
          controller.table.ajax.url(urlProducts).load();
        }else{
          controller.table.ajax.url(urlProducts+'?category='+id).load();
        }
      },
      getCategories(){
        const _this = this;
        _this.category = $.ajax({
          url: urlCategories,
          method: 'GET',
          success: function(category){
            _this.categories = JSON.parse(category);
          },
          error: function(error){
            console.log(error);
          }
        })
      },
      createCategory(){
        this.category = {};
        this.editStatus = false;
        $('#category-form').modal();
      },
      storeCategory(event, id){
        event.preventDefault();
        const _this = this;
        var urlCategory = this.editStatus ? this.urlCategories+'/'+id : this.urlCategories;
        axios.post(urlCategory, new FormData($(event.target)[0])).then(response => {
          this.getCategories();
          this.table.ajax.reload();
          $('#category-form').modal('hide');
        })
      },
      editCategory(category){
          this.category = category;
          this.editStatus = true;
          $('#category-form').modal();
        },
      deleteCategory(id){
        if(confirm("Yakin menghapus Kategori?")){
          axios.post(this.urlCategories+'/'+id, {_method: 'DELETE'}).then(response => {
            alert("Kategori berhasil dihapus.")
          });
          this.getCategories();
        }
      }
    }
  });
</script>
@endsection