<div class="modal hide hidden_remove" id="spf_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">SPF check</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Status: <strong><?php echo $status ?></strong><br /><?php echo $text ?></p>
                <p>Checked against IP: <?php echo $ip ?></p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-secondary" data-dismiss="modal">Close</a>
            </div>
            </form>
        </div>
    </div>
</div>