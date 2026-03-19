import React from 'react';
import { render, screen, fireEvent } from '@testing-library/react';
import '@testing-library/jest-dom';
import ProductDetailModal from '../components/ProductDetailModal';
import { useCart } from '../context/CartContext';
import { toast } from 'react-hot-toast';

// Mock dependencies
jest.mock('../context/CartContext', () => ({
  useCart: jest.fn(),
}));

jest.mock('react-hot-toast', () => ({
  toast: {
    success: jest.fn(),
    error: jest.fn(),
  },
}));

jest.mock('../utils/imageHelper', () => ({
  getImageUrl: (path: string) => path,
}));

describe('ProductDetailModal', () => {
    const mockItem = {
        name: 'Paracetamol',
        price: 1500,
        stock_quantity: 10,
        image: 'test.jpg',
        category: { name: 'Painkiller' }
    };

    const mockAddToCart = jest.fn();

    beforeEach(() => {
        (useCart as jest.Mock).mockReturnValue({
            addToCart: mockAddToCart,
        });
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    it('renders the product details accurately', () => {
        render(<ProductDetailModal item={mockItem} onClose={jest.fn()} />);

        expect(screen.getByText('Paracetamol')).toBeInTheDocument();
        expect(screen.getByText('Painkiller')).toBeInTheDocument();
        expect(screen.getByText('Stock: 10')).toBeInTheDocument();
    });

    it('displays the calculated subtotal for 1 item', () => {
        render(<ProductDetailModal item={mockItem} onClose={jest.fn()} />);
        
        // At quantity 1, subtotal is 1,500 MMK
        expect(screen.getByText(/1,?500/)).toBeInTheDocument();
        expect(screen.getByText('MMK')).toBeInTheDocument();
    });

    it('triggers Add to Cart logic with the current quantity', () => {
        const mockClose = jest.fn();
        render(<ProductDetailModal item={mockItem} onClose={mockClose} />);

        // The button text says "Add to Cart"
        const addToCartBtn = screen.getByText(/Add.*to Cart/i, { selector: 'button' });
        fireEvent.click(addToCartBtn);

        expect(mockAddToCart).toHaveBeenCalledWith(mockItem, 1);
        expect(toast.success).toHaveBeenCalledWith('Paracetamol added to cart!');
        expect(mockClose).toHaveBeenCalled();
    });
});
