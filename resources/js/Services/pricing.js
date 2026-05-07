const roundMoney = (value) => Math.round((Number(value || 0) + Number.EPSILON) * 100) / 100;

export function calculatePricing(subtotal, discount = 0, settings = {}) {
    const base = Math.max(Number(subtotal || 0) - Number(discount || 0), 0);
    const taxRate = Number(settings.tax_rate || 0);
    const serviceChargeRate = Number(settings.service_charge_rate || 0);
    const tax = settings.tax_enabled ? roundMoney((base * taxRate) / 100) : 0;
    const serviceCharge = settings.service_charge_enabled ? roundMoney((base * serviceChargeRate) / 100) : 0;

    return {
        base: roundMoney(base),
        tax,
        serviceCharge,
        total: roundMoney(base + tax + serviceCharge),
    };
}
