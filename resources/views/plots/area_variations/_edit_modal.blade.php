<!-- Edit Modal -->
<div class="modal fade" id="editAVModal" tabindex="-1" aria-labelledby="editAVModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="editAVForm" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Area Measurement & Statuses</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
              <input type="hidden" name="plot_id" id="modal_plot_id">

              <div class="row g-2">
                  <div class="col-md-4">
                      <label class="form-label">Plot Size (nominal)</label>
                      <input type="text" id="modal_plot_size" class="form-control" readonly>
                  </div>

                  <div class="col-md-4">
                      <label class="form-label">Measured Area</label>
                      <input type="number" step="0.01" name="measured_area"
                             id="modal_measured_area" class="form-control" required>
                  </div>

                  <div class="col-md-4">
                      <label class="form-label">Difference</label>
                      <input type="text" id="modal_difference" class="form-control" readonly>
                  </div>

                  <div class="col-md-4">
                      <label class="form-label">Measured By</label>
                      <input type="text" name="measured_by"
                             id="modal_measured_by" class="form-control">
                  </div>

                  <div class="col-md-4">
                      <label class="form-label">Measured Date</label>
                      <input type="date" name="measured_date"
                             id="modal_measured_date" class="form-control">
                  </div>

                  <div class="col-md-4">
                      <label class="form-label">Remarks</label>
                      <input type="text" name="remarks"
                             id="modal_remarks" class="form-control">
                  </div>
              </div>

              <hr>
              <h6 class="mb-2">Update Related Statuses (optional)</h6>

              <div class="row g-2">
                  <div class="col-md-4">
                      <label class="form-label">Sewer Manholes</label>
                      <select name="sewer_manholes" id="modal_sewer" class="form-select">
                          <option value="">-- keep unchanged --</option>
                          <option value="constructed">Constructed</option>
                          <option value="not_constructed">Not Constructed</option>
                      </select>
                  </div>

                  <div class="col-md-4">
                      <label class="form-label">Asphalt / Road</label>
                      <select name="asphalt_tst" id="modal_road" class="form-select">
                          <option value="">-- keep unchanged --</option>
                          <option value="yes">Yes</option>
                          <option value="no">No</option>
                      </select>
                  </div>

                  <div class="col-md-4">
                      <label class="form-label">Overall Dev Status</label>
                      <select name="overall_status" id="modal_overall" class="form-select">
                          <option value="">-- keep unchanged --</option>
                          <option value="developed">Developed</option>
                          <option value="under_development">Under Development</option>
                          <option value="not_developed">Not Developed</option>
                      </select>
                  </div>

                  <div class="col-md-4">
                      <label class="form-label">LOP Status</label>
                      <select name="lop_status" id="modal_lop" class="form-select">
                          <option value="">-- keep unchanged --</option>
                          <option value="lop">LOP</option>
                          <option value="non_lop">Non-LOP</option>
                      </select>
                  </div>

                  <div class="col-md-4">
                      <label class="form-label">Mortgage</label>
                      <select name="is_mortgaged" id="modal_mortgage" class="form-select">
                          <option value="">-- keep unchanged --</option>
                          <option value="yes">Yes</option>
                          <option value="no">No</option>
                      </select>
                  </div>

                  <div class="col-md-4">
                      <label class="form-label">Possession Status</label>
                      <select name="possession_status" id="modal_possession" class="form-select">
                          <option value="">-- keep unchanged --</option>
                          <option value="possessionable">Possessionable</option>
                          <option value="non_lop_possessionable">Non-LOP Possessionable</option>
                          <option value="under_development_possessionable">Under Development Possessionable</option>
                          <option value="not_possessionable">Not Possessionable</option>
                      </select>
                  </div>
              </div>

          </div>

          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  Cancel
              </button>
              <button type="submit" class="btn btn-primary">
                  Save Changes
              </button>
          </div>
        </div>
    </form>
  </div>
</div>
