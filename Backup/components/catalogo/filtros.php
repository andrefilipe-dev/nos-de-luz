<section class="catalogo-filtros">
    <div class="custom-main-container">
        <div class="filtros-content">

            <!-- Barra de pesquisa -->

            <div class="pesquisa-produtos">
                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="pesquisaProduto"
                    placeholder="Pesquisar produtos..."
                    autocomplete="off"
                >
            </div>

            <!-- ordenar produto -->

            <div class="ordenar-produtos">
                <label for="ordenacao">
                    Ordenar:
                </label>

                <select id="ordenacao">
                    <option value="relevancia">
                        Mais relevantes
                    </option>

                    <option value="nome-crescente">
                        Nome: A → Z
                    </option>

                    <option value="nome-decrescente">
                        Nome: Z → A
                    </option>

                    <option value="preco-crescente">
                        Menor preço
                    </option>

                    <option value="preco-decrescente">
                        Maior preço
                    </option>
                </select>
            </div>
        </div>
    </div>
</section>