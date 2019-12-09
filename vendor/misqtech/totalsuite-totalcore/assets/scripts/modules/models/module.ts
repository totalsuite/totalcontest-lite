namespace TotalCore.Modules.Models {
    import Model = TotalCore.Common.Models.Model;

    export class Module extends Model {
        getAuthorName() {
            return this.get('author.name');
        }

        getAuthorUrl() {
            return this.get('author.url');
        }

        getDescription() {
            return this.get('description');
        }

        getId() {
            return this.get('id');
        }

        getImage(name) {
            return this.get(`images.${name}`);
        }

        getLastVersion() {
            return this.get('lastVersion');
        }

        getName() {
            return this.get('name');
        }

        getPermalink() {
            return this.get('permalink');
        }

        getSource() {
            return this.get('source');
        }

        getType() {
            return this.get('type');
        }

        getVersion() {
            return this.get('version');
        }

        hasUpdate() {
            return this.get('update');
        }

        isActivated() {
            return this.get('activated');
        }

        isInstalled() {
            return this.get('installed');
        }

        isPurchased() {
            return this.get('purchased');
        }

    }
}