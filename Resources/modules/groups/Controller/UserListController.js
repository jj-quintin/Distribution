export default class UserListController {
    constructor($http, ClarolineSearchService, $stateParams, GroupAPI, clarolineAPI) {
        this.$http = $http
        this.ClarolineSearchService = ClarolineSearchService
        this.clarolineAPI = clarolineAPI
        this.$stateParams = $stateParams
        this.$uibModal = $uibModal

        this.groupId = $stateParams.groupId;
        this.group = [];
        this.users = [];
        this.search = '';
        this.savedSearch = [];
        this.selected = [];
        this.alerts = [];
        this.fields = [];

        this.baseSearch = {'field': 'group', 'id': 0, 'value': this.groupId};

        var columns = [
            {name: this.translate('username'), prop: "username", isCheckboxColumn: true, headerCheckbox: true},
            {name: this.translate('first_name'), prop: "firstName"},
            {name: this.translate('last_name'), prop:"lastName"},
            {name: this.translate('email'), prop: "mail"}
        ];

        this.dataTableOptions = {
            scrollbarV: true,
            columnMode: 'force',
            headerHeight: 50,
            footerHeight: 50,
            selectable: true,
            multiSelect: true,
            checkboxSelection: true,
            columns: columns,
            paging: {
                externalPaging: true,
                size: 10
            }
        };

        this.GroupAPI.find(this.groupId).then(d => {
            this.group = d.data;
        })

        this.$http.get(Routing.generate('api_get_user_searchable_fields')).then(d => {
            this.fields = d.data;
            //removing group from fields
            for (var i = 0; i < this.fields.length; i++) {
                if (this.fields[i] === 'group') {
                     this.fields.splice(i, 1);
                }
            }
        });
    }

    translate(key, data = {}) {
        return window.Translator.trans(key, data, 'platform');
    }

    mergeSearches(searches, element) {
        var found = false;

        for (let i = 0; i < searches.length; i++) {
            if (searches[i] == element) found = true;
        }

        if (!found) {
            searches.push(element);
        }

        return searches;
    }   

    addToGroupCallback(data) {
        this.users = this.users.concat(data);

        for (var i = 0; i < data.length; i++) {
            this.alerts.push({
                type: 'success',
                msg: translate('user_added', {'username': data[i].username})
            });
        }

        this.dataTableOptions.paging.count += data.length;
    }


    removeFromGroupCallback(data) {
        this.clarolineAPI.removeElements(this.selected, this.users);
        this.dataTableOptions.paging.count -= this.selected.length;

        for (var i = 0; i < this.selected.length; i++) {
            this.alerts.push({
                type: 'success',
                msg: translate('user_removed', {'username': this.selected[i].username})
            });
        }

        this.selected.splice(0, this.selected.length);
    }

    pickerCallback(data) {
        var userIds = [];
        var users = '';

        for (var i = 0; i < data.length; i++) {
            userIds.push(data[i]);
            users +=  data[i].username
            if (i < data.length - 1) users += ', ';
        }

        var url = Routing.generate('api_add_users_to_group', {'group': this.groupId}) + '?' + this.clarolineAPI.generateQueryString(userIds, 'userIds');

        this.clarolineAPI.confirm(
            {url: url, method: 'GET'},
            addToGroupCallback,
            this.translate('add_users_to_group'),
            this.translate('add_users_to_group_confirm', {group: this.group.name, user_list: users})
        );

    }

    clickDelete() {
        var url = Routing.generate('api_remove_users_from_group',  {'group': this.groupId}) + '?' + this.clarolineAPI.generateQueryString(this.selected, 'userIds');
        var users = '';

        for (var i = 0; i < this.selected.length; i++) {
            users +=  this.selected[i].username
            if (i < this.selected.length - 1) users += ', ';
        }

        clarolineAPI.confirm(
            {url: url, method: 'GET'},
            this.removeFromGroupCallback,
            this.translate('remove_users_from_group'),
            this.translate('remove_users_from_group_confirm', {group: this.group.name, user_list: users})
        );
    }

    onSearch(searches) {
        searches = this.mergeSearches(searches, baseSearch);
        this.savedSearch = searches;
        ClarolineSearchService.find('api_get_search_users', searches, this.dataTableOptions.paging.offset, this.dataTableOptions.paging.size).then(d => {
            this.users = d.data.users;
            this.dataTableOptions.paging.count = d.data.total;
        })
    }

    paging(offset, size) {
        this.savedSearch = mergeSearches(this.savedSearch, baseSearch);
        this.ClarolineSearchService.find('api_get_search_users', this.savedSearch, offset, size).then(d => {
            var users = d.data.users;

            //I know it's terrible... but I have no other choice with this table.
            for (var i = 0; i < offset * size; i++) {
                users.unshift({});
            }

            this.users = users;
            this.dataTableOptions.paging.count = d.data.total;
        })
    }

    addUser() {
        //maybe improve this later...
        var excludeUser = [];
        var userPicker = new UserPicker();
        var options = {
            'picker_name': 'user_group_picker',
            'picker_title': this.translate('add_user'),
            'multiple': true,
            'blacklist': excludeUser,
            'return_datas': true
        }

        userPicker.configure(options, pickerCallback);
        userPicker.open();
    }

    closeAlert(index) {
        this.alerts.splice(index, 1);
    }
}












