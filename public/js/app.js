
const app = Vue.createApp({
    data() {
        return {
            vehicleType: 'common',
            basePrice: 0,
            totalCost: 0,
            buyerFee: 0,
            sellerFee: 0,
            associationFee: 0,
            storageFee: 0,
        };
    },
    methods: {
        validateBasePrice() {
            const value = this.basePrice;

            this.basePrice = value.replace(/[^0-9.]/g, ''); //only numbers and decimal point

            // make sure only one decimal point is allowed
            if ((this.basePrice.match(/\./g) || []).length > 1) {
                this.basePrice = this.basePrice.substring(0, this.basePrice.lastIndexOf('.'));
            }

            //input validation
            if (parseFloat(this.basePrice) < 0 || isNaN(this.basePrice)) {
                this.basePrice = '0';
                alert("Invalid base price");
            }

            this.calculateTotalCost();
        },

        async calculateTotalCost() {
            // If basePrice is 0 or invalid, skip the API call
            if (this.basePrice == 0 || isNaN(this.basePrice)) {
                this.totalCost = 0;
                this.buyerFee = 0;
                this.sellerFee = 0;
                this.associationFee = 0;
                this.storageFee = 0;
                return;
            }

            try {
                const response = await fetch(`/calculate-price?basePrice=${this.basePrice}&vehicleType=${this.vehicleType}`);

                if (!response.ok){
                    throw new Error("Failed to get response");
                } 

                const data = await response.json();
                const result = data[0];
                this.totalCost = result.totalPrice;
                this.buyerFee = result.breakDown.buyerFee;
                this.sellerFee = result.breakDown.sellerFee;
                this.associationFee = result.breakDown.associationFee;
                this.storageFee = result.breakDown.storageFee;

            } catch (error) {
                console.error("Error fetching data from server:", error);
            }
        }
    },

    template: 
    `<h1 class="app-title">Vehicle Bid Calculator</h1>
      <div class="form-container">
            <div class="input-row">
                <div class="input-container">
                    <label class="input-label">Vehicle Type</label>
                    <select v-model="vehicleType" @change="calculateTotalCost" class="input-field">
                        <option value="common">Common</option>
                        <option value="luxury">Luxury</option>
                    </select>
                </div>

                <div class="input-container">
                    <label class="input-label">Base Price</label>
                    <input type="text" v-model="basePrice" @input="validateBasePrice" class="input-field" placeholder="Enter base price" />
                </div>
            </div>

            <div class="card">
                <h2 class="total-cost">Total Cost: \${{ totalCost.toFixed(2) }}</h2>
                <ul class="breakdown">
                    <li>Buyer Fee: \${{ buyerFee.toFixed(2) }}</li>
                    <li>Seller Fee: \${{ sellerFee.toFixed(2) }}</li>
                    <li>Association Fee: \${{ associationFee.toFixed(2) }}</li>
                    <li>Storage Fee: \${{ storageFee.toFixed(2) }}</li>
                </ul>
            </div>
     </div>`
});

app.mount('#app');
//background image
const bgImage = document.createElement("img");
bgImage.src = "assets/car.png"; 
bgImage.id = "backgroundImg";
document.body.appendChild(bgImage);
