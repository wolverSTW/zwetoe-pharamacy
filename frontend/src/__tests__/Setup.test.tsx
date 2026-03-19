import { render, screen } from '@testing-library/react';

describe('Baseline Setup Test', () => {
    it('renders a dummy heading to verify Jest config', () => {
        render(<h1>ZweToe Pharmacy Test Provider</h1>);
        
        const heading = screen.getByRole('heading', { level: 1 });
        expect(heading).toBeInTheDocument();
        expect(heading).toHaveTextContent('ZweToe Pharmacy Test Provider');
    });
});
