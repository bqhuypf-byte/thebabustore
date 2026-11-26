window.repairShop = function(entries) {
 return {device:'all', query:'', limit:12, entries, active:null, lastTrigger:null,
 normalize(v) {return String(v).normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/đ/g,'d').toLowerCase()},
 matches(device,text) {return (this.device==='all'||this.device===device)&&this.normalize(text).includes(this.normalize(this.query))},
 money(value) {return new Intl.NumberFormat('vi-VN').format(value)+'đ'},
 openService(id,trigger,variant=0) {this.active={...this.entries.find(e=>e.id===id), variant};this.lastTrigger=trigger;this.$refs.serviceDialog.showModal()},
 };
};
