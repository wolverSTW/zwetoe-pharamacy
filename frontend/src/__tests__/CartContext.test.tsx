import React from 'react';
import { render, screen, fireEvent } from '@testing-library/react';
import '@testing-library/jest-dom';
import { CartProvider, useCart } from '../context/CartContext';

// A mock component to consume the CartContext
const CartTester = () => {
  const { cart, addToCart, removeFromCart, updateQuantity, clearCart, totalAmount, totalItems } = useCart();

  return (
    <div>
      <h1 data-testid="total-items">Total Items: {totalItems}</h1>
      <h2 data-testid="total-amount">Total Amount: {totalAmount}</h2>
      <ul data-testid="cart-items">
        {cart.map((item: any) => (
          <li key={item.id} data-testid={`cart-item-${item.id}`}>
            {item.name} - Qty: {item.quantity} - Price: {item.price}
          </li>
        ))}
      </ul>
      <button onClick={() => addToCart({ id: 1, name: 'Aspirin', price: 50 }, 2)}>Add Aspirin</button>
      <button onClick={() => updateQuantity(1, 5)}>Update Aspirin</button>
      <button onClick={() => removeFromCart(1)}>Remove Aspirin</button>
      <button onClick={() => clearCart()}>Clear Cart</button>
    </div>
  );
};

describe('CartContext', () => {
  
  beforeEach(() => {
    // Clear localStorage before each test
    localStorage.clear();
  });

  it('provides an empty cart initially', () => {
    render(
      <CartProvider>
        <CartTester />
      </CartProvider>
    );

    expect(screen.getByTestId('total-items')).toHaveTextContent('Total Items: 0');
    expect(screen.getByTestId('total-amount')).toHaveTextContent('Total Amount: 0');
  });

  it('can add items to the cart', () => {
    render(
      <CartProvider>
        <CartTester />
      </CartProvider>
    );

    fireEvent.click(screen.getByText('Add Aspirin'));
    
    expect(screen.getByTestId('total-items')).toHaveTextContent('Total Items: 1'); // Only 1 unique item
    expect(screen.getByTestId('cart-item-1')).toHaveTextContent('Aspirin - Qty: 2 - Price: 50');
    expect(screen.getByTestId('total-amount')).toHaveTextContent('Total Amount: 100'); // 50 * 2
  });

  it('can update item quantities', () => {
    render(
      <CartProvider>
        <CartTester />
      </CartProvider>
    );

    fireEvent.click(screen.getByText('Add Aspirin'));
    fireEvent.click(screen.getByText('Update Aspirin')); // Sets qty to 5

    expect(screen.getByTestId('cart-item-1')).toHaveTextContent('Aspirin - Qty: 5 - Price: 50');
    expect(screen.getByTestId('total-amount')).toHaveTextContent('Total Amount: 250'); // 50 * 5
  });

  it('can remove items from the cart', () => {
    render(
      <CartProvider>
        <CartTester />
      </CartProvider>
    );

    fireEvent.click(screen.getByText('Add Aspirin'));
    fireEvent.click(screen.getByText('Remove Aspirin'));

    expect(screen.getByTestId('total-items')).toHaveTextContent('Total Items: 0');
    expect(screen.queryByTestId('cart-item-1')).not.toBeInTheDocument();
  });

  it('can clear the entire cart', () => {
    render(
      <CartProvider>
        <CartTester />
      </CartProvider>
    );

    fireEvent.click(screen.getByText('Add Aspirin'));
    fireEvent.click(screen.getByText('Clear Cart'));

    expect(screen.getByTestId('total-items')).toHaveTextContent('Total Items: 0');
  });

});
