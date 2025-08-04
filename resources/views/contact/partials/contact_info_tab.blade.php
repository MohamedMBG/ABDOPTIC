<span id="view_contact_page"></span>
<div class="row contact-container">
    <!-- Basic Info Section -->
    <div class="col-sm-3">
        @include('contact.contact_basic_info', ['contact' => $contact])
    </div>

    <!-- More Info Section -->
    <div class="col-sm-3">
        @include('contact.contact_more_info', ['contact' => $contact])
    </div>

    <!-- Image Section -->
    <div class="col-sm-3 text-center">
        <div class="contact-image-container">
            @if($contact->image)
                <img src="{{ asset('storage/' . $contact->image) }}" class="contact-image" alt="Contact Image">
            @else
                <img src="{{ asset('images/default-user.png') }}" class="contact-image" alt="Default Image">
            @endif
        </div>
    </div>

    <!-- Tax Info Section -->
    @if($contact->type != 'customer')
        <div class="col-sm-3">
            @include('contact.contact_tax_info', ['contact' => $contact])
        </div>
    @endif
</div>

<!-- Actions Section -->
<div class="row mt-4">
    <div class="col-sm-12 text-right">
        @if($contact->type == 'supplier' || $contact->type == 'both')
            @if(($contact->total_purchase - $contact->purchase_paid) > 0)
                <a href="{{ action([\App\Http\Controllers\TransactionPaymentController::class, 'getPayContactDue'], [$contact->id]) }}?type=purchase" class="btn btn-primary btn-sm">
                    <i class="fas fa-money-bill-alt"></i> @lang('contact.pay_due_amount')
                </a>
            @endif
        @endif
        <button type="button" class="btn btn-primary btn-sm ml-2" data-toggle="modal" data-target="#add_discount_modal">
            @lang('lang_v1.add_discount')
        </button>
    </div>
</div>

<!-- Modal for Image -->
<div id="imageModal" class="image-modal">
    <span class="close-btn">&times;</span>
    <img class="modal-content" id="fullImage">
</div>

<!-- STYLING -->
<style>
.contact-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding: 20px;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
}

/* Image Section */
.contact-image-container {
    text-align: center;
    margin: 0 auto;
}

.contact-image {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    border: 3px solid #e0e0e0;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: transform 0.3s ease, border-color 0.3s ease;
}

.contact-image:hover {
    transform: scale(1.05);
    border-color: #007bff;
}

/* Info Section */
.contact-info {
    text-align: left;
}

.customer-name {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}

.customer-tag {
    font-size: 0.9rem;
    color: #666;
    background: #e9ecef;
    padding: 4px 8px;
    border-radius: 4px;
    margin-left: 8px;
}

.contact-info p {
    margin: 8px 0;
    font-size: 0.95rem;
    color: #555;
}

.contact-info strong {
    color: #333;
}

/* Table Section */
.styled-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.styled-table th,
.styled-table td {
    padding: 12px;
    border: 1px solid #e0e0e0;
    text-align: center;
}

.styled-table th {
    background: #007bff;
    color: #fff;
    font-weight: bold;
}

.styled-table tr:nth-child(even) {
    background: #f9f9f9;
}

/* Buttons */
.btn-primary {
    background: #007bff;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
    font-size: 0.9rem;
    transition: background 0.3s ease;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-primary i {
    margin-right: 5px;
}

/* Modal Styles */
.image-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    max-width: 80%;
    max-height: 80%;
    border-radius: 10px;
    object-fit: contain;
}

.close-btn {
    position: absolute;
    top: 20px;
    right: 40px;
    font-size: 40px;
    color: white;
    cursor: pointer;
    transition: color 0.3s ease;
}

.close-btn:hover {
    color: #ff4d4d;
}

/* Responsive Design */
@media (max-width: 768px) {
    .contact-container {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .contact-info {
        text-align: center;
    }

    .contact-image {
        max-width: 80%;
    }
}
</style>

<!-- JAVASCRIPT -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    console.log("Script loaded - Waiting for image click");

    // Select modal elements
    var modal = document.getElementById("imageModal");
    var fullImage = document.getElementById("fullImage");
    var closeButton = document.querySelector(".close-btn");

    // Hide modal on load (ensures no unintended display)
    modal.style.display = "none";

    // Attach event listener to each contact image
    document.querySelectorAll(".contact-image").forEach(function (img) {
        img.addEventListener("click", function () {
            console.log("Image clicked:", img.src); // Debugging
            fullImage.src = img.src;
            modal.style.display = "flex"; // Show modal
        });
    });

    // Close modal when clicking the close button
    closeButton.addEventListener("click", function () {
        console.log("Close button clicked"); // Debugging
        modal.style.display = "none";
    });

    // Close modal when clicking outside the image
    modal.addEventListener("click", function (event) {
        if (event.target === modal) {
            console.log("Clicked outside image - closing modal"); // Debugging
            modal.style.display = "none";
        }
    });
});
</script>
