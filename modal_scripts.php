<!-- Modals -->
<div class="modal fade" id="atenderModal" tabindex="-1" role="dialog" aria-labelledby="atenderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="atenderModalLabel">Atender Proposta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formAtender">
                    <div class="form-group">
                        <label for="atenderObservacao">Observação</label>
                        <textarea class="form-control" id="atenderObservacao" name="observacao" required></textarea>
                    </div>
                    <input type="hidden" id="atenderIdProposta" name="id_proposta">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="etapaModal" tabindex="-1" role="dialog" aria-labelledby="etapaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="etapaModalLabel">Alterar Etapa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEtapa">
                    <div class="form-group">
                        <label for="proximaEtapa">Próxima Etapa</label>
                        <select class="form-control" id="proximaEtapa" name="proxima_etapa" required>
                            <!-- As opções serão carregadas pelo PHP -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="etapaObservacao">Observação</label>
                        <textarea class="form-control" id="etapaObservacao" name="observacao" required></textarea>
                    </div>
                    <input type="hidden" id="etapaIdProposta" name="id_proposta">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="declinouModal" tabindex="-1" role="dialog" aria-labelledby="declinouModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="declinouModalLabel">Atenção: A proposta será cancelada</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formDeclinou">
                    <div class="form-group">
                        <label for="declinouJustificativa">Justificativa</label>
                        <textarea class="form-control" id="declinouJustificativa" name="justificativa" required></textarea>
                    </div>
                    <input type="hidden" id="declinouIdProposta" name="id_proposta">
                    <button type="submit" class="btn btn-danger">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#atenderModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var idProposta = button.data('id');
            var modal = $(this);
            modal.find('#atenderIdProposta').val(idProposta);
        });

        $('#etapaModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var idProposta = button.data('id');
            var modal = $(this);
            modal.find('#etapaIdProposta').val(idProposta);

            // Carregar as etapas no select
            $.ajax({
                url: 'get_etapas.php',
                method: 'GET',
                success: function(data) {
                    modal.find('#proximaEtapa').html(data);
                }
            });
        });

        $('#declinouModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var idProposta = button.data('id');
            var modal = $(this);
            modal.find('#declinouIdProposta').val(idProposta);
        });

        $('#formAtender').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'atender_proposta.php',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.trim() == "success") {
                        location.reload();
                    } else {
                        alert("Erro ao salvar o atendimento.");
                    }
                }
            });
        });

        $('#formEtapa').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'alterar_etapa.php',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.trim() == "success") {
                        location.reload();
                    } else {
                        alert("Erro ao alterar a etapa.");
                    }
                }
            });
        });

        $('#formDeclinou').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'declinar_proposta.php',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.trim() == "success") {
                        location.reload();
                    } else {
                        alert("Erro ao declinar a proposta.");
                    }
                }
            });
        });
    });
</script>
