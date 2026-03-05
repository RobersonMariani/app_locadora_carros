<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component titulo="Busca de Marcas">
                    <!-- INPUTS FILTRO -->
                    <template v-slot:conteudo>
                        <div class="row">
                            <div class="col mb-3">
                                <input-container-component titulo="ID" id="inputId" id-help="idHelp"
                                    texto-ajuda="Opicional. Informe o ID da marca">
                                    <input type="number" class="form-control" id="inputId" aria-describedby="idHelp"
                                        placeholder="ID" v-model="busca.id">
                                </input-container-component>
                            </div>
                            <div class="col mb-3">
                                <input-container-component titulo="Nome" id="inputNome" id-help="nomeHelp"
                                    texto-ajuda="Opicional. Informe o nome da marca">
                                    <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp"
                                        placeholder="Nome da Marca" v-model="busca.nome">
                                </input-container-component>
                            </div>
                        </div>
                    </template>

                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-end"
                            @click="pesquisar()">Pesquisar</button>
                    </template>
                </card-component>
                <!-- INPUTS FILTRO -->

                <!-- TABELA DE DADOS -->
                <card-component titulo="Relação de Marcas" style="margin-top:20px">
                    <template v-slot:conteudo>
                        <table-component :dados="marcas.data"
                            :visualizar="{ visivel: true, dataToggle: 'modal', dataTarget: '#modalMarcaVisualiazar' }"
                            :atualizar="{ visivel: true, dataToggle: 'modal', dataTarget: '#modalMarcaAtualizar' }"
                            :remover="{ visivel: true, dataToggle: 'modal', dataTarget: '#modalMarcaRemover' }" :titulos="{
                                id: { titulo: 'ID', tipo: 'text' },
                                nome: { titulo: 'Nome', tipo: 'text' },
                                imagem: { titulo: 'Imagem', tipo: 'imagem' },
                                created_at: { titulo: 'Data de criação', tipo: 'data' },
                            }"></table-component>
                    </template>

                    <template v-slot:rodape>
                        <div class="row">
                            <div class="col-10">
                                <paginate-component>
                                    <li v-for="l, index in marcas.links" :key="index"
                                        :class="l.active ? 'page-item active' : 'page-item'" @click="paginacao(l)">
                                        <a class="page-link" v-html="l.label"></a>
                                    </li>
                                </paginate-component>
                            </div>
                            <div class="col" style="margin:auto">
                                <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal"
                                    data-bs-target="#modalMarca">Adicionar</button>
                            </div>
                        </div>
                    </template>
                </card-component>
                <!-- TABELA DE DADOS -->
            </div>
        </div>

        <!-- MODAL ADICIONAR MARCA -->
        <modal-component id="modalMarca" titulo="Adicionar Marca">
            <template v-slot:alertas>
                <alert-component tipo="success" v-if="transacaoStatus == 'adicionado'" :detalhes="transacaoDetalhes"
                    titulo="Cadastro realizado com sucesso"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca"
                    v-if="transacaoStatus == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Nome da Marca" id="novoNome" id-help="novoNomeHelp"
                        texto-ajuda="Informe o nome da marca">
                        <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp"
                            placeholder="Nome da Marca" v-model="nomeMarca">
                    </input-container-component>
                </div>

                <div class="form-group">
                    <input-container-component titulo="Imagem" id="novaImagem" id-help="novaImagemHelp"
                        texto-ajuda="Selecione uma imagem no formato PNG">
                        <input type="file" class="form-control-file" id="novaImagem" aria-describedby="novaImagemHelp"
                            placeholder="Selecione uma imagem" @change="carregarImagem($event)">
                    </input-container-component>
                </div>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>
        </modal-component>
        <!-- MODAL ADICIONAR MARCA -->

        <!-- MODAL VISUALIZAÇÃO MARCA -->
        <modal-component id="modalMarcaVisualiazar" titulo="Visualizar Marca">
            <template v-slot:alertas>
                <alert-component tipo="success" v-if="transacaoStatus == 'adicionado'" :detalhes="transacaoDetalhes"
                    titulo="Cadastro realizado com sucesso"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca"
                    v-if="transacaoStatus == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo>
                <div class="row">
                    <input-container-component titulo="" class="d-flex justify-content-center align-items-center">
                        <img :src="'storage/' + item.imagem" width="80" height="80" alt="logo" v-if="item.imagem">
                    </input-container-component>
                    <input-container-component titulo="ID">
                        <input type="text" class="form-control" :value="item.id" disabled>
                    </input-container-component>
                </div>
                <input-container-component titulo="Nome da Marca">
                    <input type="text" class="form-control" :value="item.nome" disabled>
                </input-container-component>
                <input-container-component titulo="Data de Criação">
                    <input type="text" class="form-control" :value="formatarData(item.created_at)" disabled>
                </input-container-component>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </template>
        </modal-component>
        <!-- MODAL VISUALIZAÇÃO MARCA -->

        <!-- MODAL REMOÇÃO MARCA -->
        <modal-component id="modalMarcaRemover" titulo="Remover Marca">
            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Transação realizada com sucesso"
                    :detalhes="{ mensagem: transacao.mensagem }" v-if="transacao.status == 'sucesso'"></alert-component>
                <alert-component tipo="danger" titulo="Erro na transação" :detalhes="{ mensagem: transacao.mensagem }"
                    v-if="transacao.status == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo v-if="transacao.status != 'sucesso'">
                <div class="row">
                    <input-container-component titulo="" class="d-flex justify-content-center align-items-center">
                        <img :src="'storage/' + item.imagem" width="80" height="80" alt="logo" v-if="item.imagem">
                    </input-container-component>
                    <input-container-component titulo="ID">
                        <input type="text" class="form-control" :value="item.id" disabled>
                    </input-container-component>
                </div>
                <input-container-component titulo="Nome da Marca">
                    <input type="text" class="form-control" :value="item.nome" disabled>
                </input-container-component>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-danger" @click="remover"
                    v-if="transacao.status != 'sucesso'">Remover</button>
            </template>
        </modal-component>
        <!-- MODAL REMOÇÃO MARCA -->

        <!-- MODAL ATUALIZAÇÃO MARCA -->
        <modal-component id="modalMarcaAtualizar" titulo="Atualizar Marca">
            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Transação realizada com sucesso"
                    :detalhes="{ mensagem: transacao.mensagem }" v-if="transacao.status == 'sucesso'"></alert-component>
                <alert-component tipo="danger" titulo="Erro na transação" :detalhes="{ mensagem: transacao.mensagem }"
                    v-if="transacao.status == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Nome da Marca" id="atualizarNome" id-help="atualizarNomeHelp"
                        texto-ajuda="Informe o nome da marca">
                        <input type="text" class="form-control" id="atualizarNome" aria-describedby="atualizarNomeHelp"
                            placeholder="Nome da Marca" v-model="item.nome">
                    </input-container-component>
                </div>

                <div class="form-group">
                    <input-container-component titulo="Imagem" id="atualizarImagem" id-help="atualizarImagemHelp"
                        texto-ajuda="Selecione uma imagem no formato PNG">
                        <input type="file" class="form-control-file" id="atualizarImagem"
                            aria-describedby="atualizarImagemHelp" placeholder="Selecione uma imagem"
                            @change="carregarImagem($event)">
                    </input-container-component>
                </div>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="atualizar()">Atualizar</button>
            </template>
        </modal-component>
        <!-- MODAL ATUALIZAÇÃO MARCA -->
    </div>
</template>
    
<script>
import axios from 'axios';
import Paginate from './Paginate.vue';
import InputContainer from './InputContainer.vue';
import { mapState } from 'vuex';
export default {
    components: { Paginate, InputContainer },
    data() {
        return {
            urlBase: 'http://localhost:8000/api/v1/marca',
            urlPaginacao: '',
            urlFiltro: '',
            nomeMarca: '',
            arquivoImagem: [], //trabalhar com array quando o input for file,
            transacaoStatus: '',
            transacaoDetalhes: {},
            marcas: { data: [] },
            busca: { id: '', nome: '' }
        }
    },
    computed: {
        ...mapState(['item', 'transacao']),
    },
    methods: {
        atualizar() {
            let formData = new FormData()
            formData.append('_method', 'patch')
            formData.append('nome', this.item.nome)
            if (this.arquivoImagem[0]) {
                formData.append('imagem', this.arquivoImagem[0])
            }
            let url = this.urlBase + '/' + this.item.id
            let config = {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }

            axios.post(url, formData, config)
                .then(response => {
                    this.transacao.status = 'sucesso'
                    this.transacao.mensagem = 'Registro de marca atualizado com sucesso'
                    atualizarImagem.value = ''
                    this.carregarLista()
                })
                .catch(errors => {
                    this.transacao.status = 'erro'
                    this.transacao.mensagem = errors.response.data.message
                    this.transacao.dados = errors.response.data.errors
                })
        },
        remover() {
            let confirmacao = confirm('Tem certeza que desja remover esse registro?')
            if (!confirmacao) {
                return false
            }
            let url = this.urlBase + '/' + this.item.id
            let formData = new FormData();
            formData.append('_method', 'delete')
            axios.post(url, formData)
                .then(response => {
                    this.transacao.status = 'sucesso'
                    this.transacao.mensagem = response.data.msg
                    this.carregarLista()
                })
                .catch(errors => {
                    this.transacao.status = 'erro'
                    this.transacao.mensagem = errors.response.data.erro
                })
        },
        pesquisar() {
            let filtro = ''
            for (let chave in this.busca) {
                if (this.busca[chave]) {
                    if (filtro != '') {
                        filtro += ';'
                    }
                    filtro += chave + ':like:' + '%' + this.busca[chave] + '%'
                }
            }
            if (filtro) {
                this.urlPaginacao = 'page=1'
                this.urlFiltro = '&filtro=' + filtro
            } else {
                this.urlFiltro = ''
            }
            this.carregarLista()
        },
        paginacao(l) {
            if (l.url) {
                //this.urlBase = l.url //ajustando a url de consulta com o parâmetro de página
                this.urlPaginacao = l.url.split('?')[1]
                this.carregarLista() //requisitando novamente os dados para nossa API
            }
        },
        carregarLista() {
            let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro
            axios.get(url)
                .then(response => {
                    this.marcas = response.data
                })
                .catch(errors => {
                    console.log(errors)
                })
        },
        carregarImagem(e) {
            this.arquivoImagem = e.target.files;
        },
        salvar() {
            let formData = new FormData();
            formData.append('nome', this.nomeMarca)
            formData.append('imagem', this.arquivoImagem[0])

            let config = {
                headers: {
                    'Content-Type': 'multipart/form-data',
                }
            }
            axios.post(this.urlBase, formData, config)
                .then(response => {
                    this.transacaoStatus = 'adicionado'
                    this.transacaoDetalhes = {
                        mensagem: 'ID do registro: ' + response.data.id
                    }
                    this.carregarLista()
                })
                .catch(errors => {
                    this.transacaoStatus = 'erro'
                    this.transacaoDetalhes = {
                        mensagem: errors.response.data.message,
                        dados: errors.response.data.errors
                    }
                })
        },
        formatarData(data) {
            let novaData = new Date(data);
            return novaData.toLocaleDateString('pt-BR', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' });
        },
    },
    mounted() {
        this.carregarLista()
    },
}
</script>