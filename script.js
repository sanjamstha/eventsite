    function filterEvents() {
        // Get the search keyword from the input field
        const searchInput = document.getElementById("searchInput").value.toLowerCase();
    
        // Get all event cards
        const eventCards = document.querySelectorAll(".eventCard");
    
        // Loop through each event card and check if the title includes the search keyword
        eventCards.forEach(card => {
        const eventTitle = card.getAttribute("data-title").toLowerCase(); // Get event title from the data-title attribute
        if (eventTitle.includes(searchInput)) {
            card.style.display = "block";  // Show the event card
        } else {
            card.style.display = "none";  // Hide the event card
        }
        });
    }
    
