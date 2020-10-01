import axios from "axios";

export default class Task {
    constructor(task = {}) {
        this.rules = {
            name: 'required|max:150',
            description: 'required|max:1500'
        };

        this.id = task.id || null;
        this.name = task.name || null;
        this.description = task.description || null;
    }

    createFormData(){
        let formData = new FormData()
        formData.append('name', this.name)
        formData.append('description', this.description)
        return formData;
    }

    create(){
        return axios.post('/manager/task', this.createFormData());
    }

    appoint(user_id){
        return axios.post('/manager/task/appoint', {task_id: this.id, user_id: user_id});
    }

    changeStatus(status_id){
        return axios.post('/user/task/change-status', {task_id: this.id, status_id: status_id});
    }

    update(){
        let data = this.createFormData();
        data.append('_method', 'put');
        return axios.post('/manager/task/'+this.id, data);
    }

    delete(){
        return axios.delete('/manager/task/'+this.id);
    }
}
