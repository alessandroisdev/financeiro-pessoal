import 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js';
import 'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js';
export default class DataTableInit {
    constructor(selector, config) {
        const defaultConfig = {
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
        const finalConfig = {
            ...defaultConfig,
            ...config
        };
        this.table = window.$(selector).DataTable(finalConfig);
    }
    reload(resetPaging = false) {
        this.table.ajax.reload(null, resetPaging);
    }
    api() {
        return this.table;
    }
}
//# sourceMappingURL=datatable.js.map