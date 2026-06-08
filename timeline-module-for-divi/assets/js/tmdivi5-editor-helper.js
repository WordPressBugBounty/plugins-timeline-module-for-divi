class Tmdivi5_Editor_Helper {
	constructor() {
		this.init();
	}

	/**
	 * Initialize the class
	 */
	init() {
		this.bindEvents();
	}

	/**
	 * Bind event listeners
	 */
	bindEvents() {
		jQuery(document).on('pointerdown', '.et-vb-modal-group-title[data-name="layout_setting"]', this.handleD5FieldTypeOpen.bind(this));
	}

    handleD5FieldTypeOpen(e){
        setTimeout(()=>{
            let currentTarget = jQuery(e.currentTarget)
            let nextWrap = currentTarget.closest('.et-vb-modal-group').find('.et-vb-modal-group-content')
            let placeholderText = nextWrap.find('li.select-option-item-placeholder .select-option-item__name')
            if(placeholderText.text() == ''){
                placeholderText.text('Both Side')
            }
        },100)
    }
}

jQuery(document).ready(function () {
	new Tmdivi5_Editor_Helper();
});