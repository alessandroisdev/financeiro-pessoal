import 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js';
import 'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js';

export interface DataTableConfig extends DataTables.Settings {}

export default class DataTableInit {
    private table: DataTables.Api;

    /**
     * @param selector Seletor CSS da tabela (ex.: '#datatable-accounts')
     * @param config   Configuração específica dessa tabela
     */
    constructor(selector: string, config: DataTableConfig) {
        const defaultConfig: DataTableConfig = {
            processing: true,
            serverSide: true,
            responsive: true,
            searching: true,
            ordering: true,
            paging: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
            }
        };

        // Mescla configs (config sobrescreve defaults)
        const finalConfig: DataTableConfig = {
            ...defaultConfig,
            ...config
        };

        this.table = (window as any).$(selector).DataTable(finalConfig);
    }

    /**
     * Recarrega os dados do DataTable respeitando os filtros atuais.
     */
    public reload(resetPaging: boolean = false): void {
        this.table.ajax.reload(null, resetPaging);
    }

    /**
     * Retorna a instância nativa do DataTables (caso queira acessar diretamente).
     */
    public api(): DataTables.Api {
        return this.table;
    }
}
