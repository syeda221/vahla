<!-- Multi-Warehouse Stock Allocation Modal -->
<div class="modal fade" id="multiWarehouseModal" tabindex="-1" role="dialog" aria-labelledby="multiWarehouseModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header py-3 px-4 text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="multiWarehouseModalLabel">
                        <i class="fas fa-boxes text-warning me-2"></i> Multi-Location / Warehouse Stock Dispatch
                    </h5>
                    <small class="text-white-50" style="font-size: 0.78rem;">Ek hi product ko Shop aur Warehouse se alag alag quantity mein sell karein (aik hi row mein gather hoga)</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.85; font-size: 1.5rem; background: none; border: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" style="background-color: #f8fafc;">
                <!-- Product Overview Card -->
                <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px;">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <span class="badge bg-primary text-white mb-1 font-monospace" style="font-size: 0.7rem;" id="mwhProductCode">ITEM-CODE</span>
                                <h6 class="fw-bold text-dark mb-1" id="mwhProductName">Product Name</h6>
                                <div class="text-muted small" style="font-size: 0.76rem;" id="mwhProductMeta">Price: Rs. 0.00 | Unit: Pcs</div>
                            </div>
                            <div class="col-md-5 text-md-end mt-2 mt-md-0">
                                <label class="fw-bold text-secondary small d-block mb-1">Total Quantity Needed:</label>
                                <div class="input-group input-group-sm justify-content-md-end" style="max-width: 210px; margin-left: auto;">
                                    <input type="number" step="any" min="0.01" class="form-control text-center fw-bold fs-6" id="mwhTotalQty" style="border-color: #2563eb; background: #eff6ff;" placeholder="0">
                                    <span class="input-group-text bg-primary text-white fw-bold" id="mwhUnitLabel">Qty</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Warehouses Stock & Allocation Table -->
                <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px; overflow: hidden;">
                    <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark small"><i class="fas fa-warehouse text-primary me-1"></i> Available Stock Across Locations</span>
                        <button type="button" class="btn btn-xs btn-outline-primary fw-semibold" id="mwhBtnAutoDistribute" title="Auto distribute quantity starting from Shop, then Godowns">
                            <i class="fas fa-magic me-1"></i> Auto Allocate (Shop First)
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0" id="mwhWarehousesTable">
                            <thead class="table-light small text-secondary">
                                <tr>
                                    <th style="width: 40px;" class="text-center">#</th>
                                    <th>Location / Store</th>
                                    <th style="width: 150px;" class="text-center">Available Stock</th>
                                    <th style="width: 170px;" class="text-center">Dispatch Qty</th>
                                    <th style="width: 75px;" class="text-center">Quick</th>
                                </tr>
                            </thead>
                            <tbody id="mwhWarehousesBody">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Live Allocation Balance Alert Box -->
                <div class="d-flex justify-content-between align-items-center p-3 rounded-3 transition-all" id="mwhAllocationStatusBox" style="background: #e2e8f0; border: 1px solid #cbd5e1;">
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <span class="text-muted small d-block" style="font-size:0.72rem;">TOTAL NEEDED:</span>
                            <strong class="text-dark fs-6" id="mwhDispNeeded">0</strong>
                        </div>
                        <div class="border-start ps-3">
                            <span class="text-muted small d-block" style="font-size:0.72rem;">ALLOCATED:</span>
                            <strong class="text-primary fs-6" id="mwhDispAllocated">0</strong>
                        </div>
                        <div class="border-start ps-3">
                            <span class="text-muted small d-block" style="font-size:0.72rem;">REMAINING:</span>
                            <strong class="text-danger fs-6" id="mwhDispRemaining">0</strong>
                        </div>
                    </div>
                    <div>
                        <span class="badge px-3 py-2 fw-bold" id="mwhStatusBadge" style="font-size: 0.8rem;">
                            Enter Quantities
                        </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top py-2 px-4 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm" id="mwhBtnApply">
                    <i class="fas fa-check-circle me-1"></i> Apply Allocation (Gather in Row)
                </button>
            </div>
        </div>
    </div>
</div>
