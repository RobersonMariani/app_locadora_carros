<template>
    <div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th v-for="t, key in titulos" :key="key" scope="col">{{ t.titulo }}</th>
                    <th v-if="visualizar.visivel || atualizar.visivel || remover.visivel" style="text-align:center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="obj, chave in dadosFiltrados" :key="chave">
                    <td v-for="valor, chaveValor in obj" :key="chaveValor">
                        <span v-if="titulos[chaveValor].tipo == 'text'">{{ valor }}</span>
                        <span v-if="titulos[chaveValor].tipo == 'data'">{{ formatarData(valor) }}</span>
                        <span v-if="titulos[chaveValor].tipo == 'imagem'">
                            <img :src="'/storage/' + valor" width="55" height="55" alt="logo">
                        </span>
                    </td>
                    <td v-if="visualizar.visivel || atualizar.visivel || remover.visivel">
                        <button v-if="visualizar.visivel" class="btn btn-outline-primary btn-sm m-1"
                            :data-bs-toggle="visualizar.dataToggle" :data-bs-target="visualizar.dataTarget"
                            @click="setStore(obj)">Visualizar</button>
                        <button v-if="atualizar.visivel" :data-bs-toggle="atualizar.dataToggle"
                            :data-bs-target="atualizar.dataTarget" class="btn btn-outline-success btn-sm" @click="setStore(obj)">Atualizar</button>
                        <button v-if="remover.visivel" :data-bs-toggle="remover.dataToggle"
                            :data-bs-target="remover.dataTarget" class="btn btn-outline-danger btn-sm m-1"
                            @click="setStore(obj)">Remover</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import { mapState } from 'vuex';
export default {
    filters: {
        formataDataTempo(d){
            if(!d) return ''
            d = d.split('T')
            let data = d[0]
            let tempo = d[1]
            data = data.split('-')
            data = data[2] + '/' + data[1] + '/' + data[0]
            tempo = tempo.split('.')
            tempo = tempo[0]
            return data + ' ' + tempo
        }
    },
    props: ['dados', 'titulos', 'atualizar', 'visualizar', 'remover'],
    computed: {
        ...mapState(['transacao']),
        dadosFiltrados() {
            let campos = Object.keys(this.titulos)
            let dadosFiltrados = []
            this.dados.map((item, chave) => {
                let itemFiltrado = {}
                campos.forEach(campo => {
                    itemFiltrado[campo] = item[campo]
                })
                dadosFiltrados.push(itemFiltrado)
            })
            return dadosFiltrados
        }
    },
    methods: {
        formatarData(data) {
            let formattedDate = this.$formataDataTempo(data)
            return formattedDate
            /* let novaData = new Date(data);
            return novaData.toLocaleDateString('pt-BR', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' }); */
        },
        setStore(obj) {
            this.transacao.status = ''
            this.transacao.mensagem = ''
            this.transacao.dados = ''
            this.$store.commit('GET_DATA', obj)
        }
    }
}
</script>
