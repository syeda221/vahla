<!-- ================= LINKED / COMPLEMENTARY PRODUCTS PROMPT MODAL ================= -->
<div class="modal fade" id="linkedProductsModal" tabindex="-1" role="dialog" aria-labelledby="linkedProductsModalLabel" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 820px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden; border: 1.5px solid #7c3aed !important;">
            <div class="modal-header py-2 px-3 text-white" style="background: linear-gradient(135deg, #4c1d95 0%, #6d28d9 100%); border-bottom: 2px solid #5b21b6;">
                <div class="d-flex align-items-center gap-2">
                    <div style="background: rgba(255,255,255,0.2); width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-link text-white" style="font-size: 1rem;"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" id="linkedProductsModalLabel" style="font-size: 0.95rem; letter-spacing: 0.3px;">
                            Complementary Products Linked With This Item
                        </h6>
                        <small class="text-white-50" style="font-size: 0.76rem;">
                            Selected: <strong class="text-white" id="lp_prompt_parent_name">-</strong> (<span id="lp_prompt_parent_code" class="font-monospace text-warning">-</span>)
                        </small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.4rem; line-height: 1; opacity: 0.85; cursor: pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3" style="background: #f8fafc; max-height: calc(85vh - 140px); overflow-y: auto;">
                <div class="alert alert-info py-2 px-3 mb-2 d-flex align-items-center justify-content-between" style="border-radius: 8px; font-size: 0.8rem; background: #ede9fe; color: #5b21b6; border-color: #ddd6fe;">
                    <span>
                        <i class="fas fa-info-circle me-1"></i> These products are frequently sold or linked together. Select items to add them to this invoice.
                    </span>
                    <span class="badge bg-purple text-white px-2 py-1" style="background: #7c3aed; font-size: 0.72rem;">
                        <span id="lp_prompt_items_count">0</span> Linked Item(s)
                    </span>
                </div>

                <div class="card border shadow-sm" style="border-radius: 10px; overflow: hidden; background: #ffffff;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="lpPromptTable">
                            <thead style="background: #0f172a; color: #f8fafc; font-size: 0.75rem; text-transform: uppercase;">
                                <tr>
                                    <th style="width: 40px; padding: 8px 12px;" class="text-center">
                                        <input type="checkbox" id="lp_check_all" checked style="cursor: pointer; transform: scale(1.15);">
                                    </th>
                                    <th style="padding: 8px 12px;">LINKED PRODUCT &amp; VARIANT</th>
                                    <th style="width: 130px; padding: 8px 12px;" class="text-center">CURRENT STOCK</th>
                                    <th style="width: 120px; padding: 8px 12px;" class="text-end">RATE (RS.)</th>
                                    <th style="width: 130px; padding: 8px 12px;" class="text-center">ADD QTY</th>
                                    <th style="width: 120px; padding: 8px 12px;" class="text-end">LINE TOTAL</th>
                                </tr>
                            </thead>
                            <tbody id="lpPromptTableBody" style="font-size: 0.84rem;">
                                <!-- Dynamic rows injected here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer py-2 px-3 bg-white border-top d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span style="font-size: 0.82rem; color: #475569;">
                        Selected: <strong class="text-dark" id="lp_selected_count_label">0 items</strong>
                    </span>
                    <span style="font-size: 0.82rem; color: #475569;">
                        Total Additional: <strong class="text-success font-monospace" id="lp_selected_total_label">Rs. 0.00</strong>
                    </span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3 fw-bold" id="btnSkipLinkedProducts" data-dismiss="modal" data-bs-dismiss="modal" style="font-size: 0.8rem;">
                        Skip (Esc)
                    </button>
                    <button type="button" class="btn btn-sm text-white fw-bold px-3 shadow-sm d-flex align-items-center gap-1" id="btnAddLinkedProductsToSale" style="background: #7c3aed; border: 1.5px solid #6d28d9; border-radius: 6px; font-size: 0.82rem;">
                        <i class="fas fa-plus-circle"></i> Add Selected to Sale (Enter)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
