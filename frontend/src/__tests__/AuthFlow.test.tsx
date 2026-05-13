import React from 'react';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import '@testing-library/jest-dom';
import LoginPage from '../app/login/page';
import RegisterPage from '../app/register/page';
import axios from 'axios';
import { authService } from '@/services/authService';

jest.mock('axios');
jest.mock('@/services/authService', () => ({
  authService: {
    register: jest.fn(),
  },
}));

describe('AuthFlow - Login & Registration', () => {
  beforeEach(() => {
    localStorage.clear();
  });

  afterEach(() => {
    jest.clearAllMocks();
  });

  describe('LoginPage', () => {
    it('renders login form correctly', () => {
      render(<LoginPage />);
      expect(screen.getByPlaceholderText(/name@email.com/i)).toBeInTheDocument();
      expect(document.querySelector('input[name="password"]')).not.toBeNull();
      expect(screen.getByRole('button', { name: /Sign In/i })).toBeInTheDocument();
    });

    it('displays error message on failed login', async () => {
      (axios.post as jest.Mock).mockRejectedValueOnce({
        response: { data: { message: 'Invalid credentials' } },
      });

      render(<LoginPage />);
      const passwordInput = document.querySelector('input[name="password"]') as HTMLInputElement;

      fireEvent.change(screen.getByPlaceholderText(/name@email.com/i), { target: { value: 'test@mail.com' } });
      fireEvent.change(passwordInput, { target: { value: 'password123' } });
      fireEvent.click(screen.getByRole('button', { name: /Sign In/i }));

      await waitFor(() => {
        expect(screen.getByText(/Invalid credentials/i)).toBeInTheDocument();
      });
    });

    it('saves token to localStorage on successful login', async () => {
      (axios.post as jest.Mock).mockResolvedValueOnce({
        data: { token: 'mock-token', user: { name: 'Test User' } },
      });

      const locationMock = new URL('http://localhost');
      delete (window as any).location;
      window.location = locationMock as any;

      render(<LoginPage />);
      const passwordInput = document.querySelector('input[name="password"]') as HTMLInputElement;

      fireEvent.change(screen.getByPlaceholderText(/name@email.com/i), { target: { value: 'test@mail.com' } });
      fireEvent.change(passwordInput, { target: { value: 'password123' } });
      fireEvent.click(screen.getByRole('button', { name: /Sign In/i }));

      await waitFor(() => {
        expect(localStorage.getItem('token')).toBe('mock-token');
        expect(window.location.href).toBe('http://localhost/');
      });
    });
  });

  describe('RegisterPage', () => {
    it('renders registration form correctly', () => {
      render(<RegisterPage />);
      expect(screen.getByPlaceholderText(/Your Name/i)).toBeInTheDocument();
      expect(screen.getByPlaceholderText(/09XXXXXXXXX/i)).toBeInTheDocument();
      expect(screen.getByPlaceholderText(/example@mail.com/i)).toBeInTheDocument();
    });

    it('shows the pending approval success screen after registration', async () => {
      (authService.register as jest.Mock).mockResolvedValueOnce({
        status: 'success',
      });

      render(<RegisterPage />);

      fireEvent.change(screen.getByPlaceholderText(/Your Name/i), { target: { value: 'John Doe' } });
      fireEvent.change(screen.getByPlaceholderText(/09XXXXXXXXX/i), { target: { value: '09123456789' } });
      fireEvent.change(screen.getByPlaceholderText(/example@mail.com/i), { target: { value: 'john@mail.com' } });
      fireEvent.change(screen.getByPlaceholderText(/Min. 8 characters/i), { target: { value: 'pass1234' } });
      fireEvent.change(screen.getByPlaceholderText(/Re-enter password/i), { target: { value: 'pass1234' } });

      fireEvent.click(screen.getByRole('button', { name: /Register Now/i }));

      await waitFor(() => {
        expect(screen.getByRole('heading', { name: /Account Created!/i })).toBeInTheDocument();
        expect(screen.getByText(/pending admin approval/i)).toBeInTheDocument();
      });
    });
  });
});
