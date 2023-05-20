  <div class="modal modal-blur fade" id="create-product" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Produk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form :action="urlProducts" method="POST" @submit="storeProduct($event)">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input type="text" class="form-control" name="name" placeholder="Nama Produk">
            </div>
            <div class="row">
              <div class="col-lg-5">
                <div class="mb-3">
                  <label class="form-label">Kategori</label>
                  <select class="form-select" name="category">
                    <option v-for="category in categories" :value="category.id">@{{ category.name }}</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-3">
                <div class="mb-3">
                  <label class="form-label">Stok</label>
                  <input type="number" class="form-control" name="stock" placeholder="Stok Tersedia">
                </div>
              </div>
              <div class="col-lg-4">
                <div class="mb-3">
                  <label class="form-label">Harga</label>
                  <input type="number" class="form-control" name="price" placeholder="Harga Jual">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
              Cancel
            </a>
            <button type="submit" class="btn btn-primary ms-auto" data-bs-dismiss="modal">Create new report</button>
          </div>
        </form>
      </div>
    </div>
  </div>