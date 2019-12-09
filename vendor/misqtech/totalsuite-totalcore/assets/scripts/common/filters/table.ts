///<reference path="../../common/decorators.ts"/>
///<reference path="../helpers.ts"/>
namespace TotalCore.Common.Filters {
    enum TABLE_FILTER_MODES {
        Horizontal = 'horizontal',
        Vertical = 'vertical',
        Group = 'group',
    }

    enum TABLE_CELL_TYPES {
        Header = 'th',
        Data = 'td',
    }

    enum TABLE_GROUP_TYPES {
        Header = 'thead',
        Body = 'tbody',
        Footer = 'tfoot',
    }

    @Filter('filters.common', 'table')
    export class Table {
        constructor($sce) {
            return (input: any, mode: TABLE_FILTER_MODES = TABLE_FILTER_MODES.Vertical) => {
                return $sce.trustAsHtml(Table.filter(input, mode));
            }
        }

        public static filter(obj, mode: TABLE_FILTER_MODES, level = 1) {
            if (!angular.isObject(obj) && !angular.isArray(obj)) {
                return obj.toString();
            }

            let header = [];
            let body = [];
            let footer = [];

            angular.forEach(obj, (value, key) => {
                if ((angular.isObject(value) || angular.isArray(value)) && mode !== TABLE_FILTER_MODES.Group) {
                    value = Table.filter(value, mode, level + 1);
                } else if (value === null) {
                    return;
                }

                switch (mode) {
                    case TABLE_FILTER_MODES.Horizontal:
                        header.push(Table.cell(key, TABLE_CELL_TYPES.Header));
                        if (!body[0]) {
                            body[0] = [];
                        }
                        body[0].push([Table.cell(value)]);

                        break;
                    case TABLE_FILTER_MODES.Vertical:
                        body.push([Table.cell(key), Table.cell(value)]);
                        break;
                    case TABLE_FILTER_MODES.Group:
                        if (header.length === 0) {
                            header = extract(value, EXTRACT_TYPE.Keys).map(item => Table.cell(item, TABLE_CELL_TYPES.Header));
                        }
                        body.push(extract(value, EXTRACT_TYPE.Values).map(item => Table.cell(item)));
                        break;
                }
            });

            return Table.table(header, body.map(Table.row).join(''), footer);
        }

        private static cell(value, type: TABLE_CELL_TYPES = TABLE_CELL_TYPES.Data) {
            return `<${type}>${value}</${type}>`;
        }

        private static row(cells) {
            return cells.length ? `<tr>${cells.join(' ')}</tr>` : '';
        }

        private static group(rows, type: TABLE_GROUP_TYPES = TABLE_GROUP_TYPES.Body) {
            return `<${type}>${angular.isArray(rows) ? rows.join('') : rows}</${type}>`;
        }

        private static table(header, body, footer) {
            return `<table class="widefat">
                        ${this.group(header, TABLE_GROUP_TYPES.Header)}
                        ${this.group(body, TABLE_GROUP_TYPES.Body)}
                        ${this.group(footer, TABLE_GROUP_TYPES.Footer)}
                    </table>`;
        }

    }
}