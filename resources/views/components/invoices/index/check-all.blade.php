<div x-data="checkAll" class="relative flex items-center cursor-pointer w-fit">
    <input x-ref="checkbox" @change="handleCheck" type="checkbox" class="peer h-4 w-4 cursor-pointer transition-all appearance-none rounded border border-slate-300 checked:bg-blue-700 indeterminate:bg-blue-700 checked:border-blue-700 indeterminate:border-blue-700" />
    <span class="absolute peer-checked:bg-blue-700 text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none">
        <x-svg.check class="text-white h-3 w-3" />
    </span>
    <span class="absolute peer-indeterminate:bg-blue-700 text-white opacity-0 peer-indeterminate:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none">
        <x-svg.indeterminate class="text-white h-3 w-2.5" />
    </span>
</div>

@script
<script>
    Alpine.data('checkAll', () => {
        return {
            init() {
                this.$wire.$watch('selectedInvoiceIds', () => {
                    this.updateCheckAllState()
                })

                this.$wire.$watch('invoiceIdsOnPage', () => {
                    this.updateCheckAllState()
                })

                // État initial
                this.updateCheckAllState()
            },

            updateCheckAllState() {
                if (this.pageIsSelected()) {
                    this.$refs.checkbox.checked = true
                    this.$refs.checkbox.indeterminate = false
                } else if (this.pageIsEmpty()) {
                    this.$refs.checkbox.checked = false
                    this.$refs.checkbox.indeterminate = false
                } else {
                    this.$refs.checkbox.checked = false
                    this.$refs.checkbox.indeterminate = true
                }
            },

            pageIsSelected() {
                return this.$wire.invoiceIdsOnPage.every(id => this.$wire.selectedInvoiceIds.includes(id))
            },

            pageIsEmpty() {
                return this.$wire.selectedInvoiceIds.length === 0
            },

            handleCheck(e) {
                e.target.checked ? this.selectAll() : this.deselectAll()
            },

            selectAll() {
                this.$wire.invoiceIdsOnPage.forEach(id => {
                    if (this.$wire.selectedInvoiceIds.includes(id)) return

                    this.$wire.selectedInvoiceIds.push(id)
                })
            },

            deselectAll() {
                this.$wire.selectedInvoiceIds = []
            },
        }
    })
</script>
@endscript
