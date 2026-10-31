{{-- Chin Jia Jun --}}

<style>
    #book-partial {
        text-decoration: none;
        background-color: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-radius: 10px;
        border: none;
        font-size: 18px;
        font-weight: bold;
        padding: 10px;
        position: fixed;
        right: 50px;
        bottom: 50px;
        z-index: 5;
    }

    #book-partial:hover {
        background-color: gray;
        color: white;
        border: 1px solid black;
        transform: scale(1.1);
        transition: transform 0.3s ease-in-out;
    }
</style>

<a href="{{ url('booking/create') }}" id="book-partial">
    BOOK NOW
</a>