import { computed, Injectable, signal } from '@angular/core';
import { Preferences } from '@capacitor/preferences';

export interface CartProduct {
  id: number;
  name: string;
  price: string;
  stock: number;
}

export interface CartItem {
  product_id: number;
  name: string;
  price: number;
  stock: number;
  quantity: number;
}

@Injectable({ providedIn: 'root' })
export class CartService {
  private readonly storageKey = 'motoserve_customer_cart';

  readonly items = signal<CartItem[]>([]);
  readonly count = computed(() =>
    this.items().reduce((total, item) => total + item.quantity, 0)
  );
  readonly subtotal = computed(() =>
    this.items().reduce(
      (total, item) => total + item.price * item.quantity,
      0
    )
  );

  private readonly ready = this.restore();

  private async restore(): Promise<void> {
    try {
      const { value } = await Preferences.get({
        key: this.storageKey,
      });

      if (value) {
        const saved: unknown = JSON.parse(value);
        if (Array.isArray(saved)) {
          this.items.set(saved as CartItem[]);
        }
      }
    } catch {
      this.items.set([]);
    }
  }

  async add(product: CartProduct): Promise<void> {
    await this.ready;

    const price = Number(product.price);
    if (!Number.isFinite(price) || price < 0) {
      throw new Error('Invalid product price.');
    }
    if (product.stock < 1) {
      throw new Error('This product is out of stock.');
    }

    const existing = this.items().find(
      item => item.product_id === product.id
    );

    if (existing && existing.quantity >= product.stock) {
      throw new Error('Maximum available stock reached.');
    }

    this.items.update(items =>
      existing
        ? items.map(item =>
            item.product_id === product.id
              ? {
                  ...item,
                  quantity: item.quantity + 1,
                  stock: product.stock,
                  price,
                }
              : item
          )
        : [
            ...items,
            {
              product_id: product.id,
              name: product.name,
              price,
              stock: product.stock,
              quantity: 1,
            },
          ]
    );

    await this.save();
  }

  async changeQuantity(
    productId: number,
    quantity: number
  ): Promise<void> {
    await this.ready;

    const item = this.items().find(
      entry => entry.product_id === productId
    );
    if (!item) return;

    if (quantity <= 0) {
      await this.remove(productId);
      return;
    }

    if (quantity > item.stock) {
      throw new Error('Maximum available stock reached.');
    }

    this.items.update(items =>
      items.map(entry =>
        entry.product_id === productId
          ? { ...entry, quantity }
          : entry
      )
    );

    await this.save();
  }

  async remove(productId: number): Promise<void> {
    await this.ready;
    this.items.update(items =>
      items.filter(item => item.product_id !== productId)
    );
    await this.save();
  }

  async clear(): Promise<void> {
    await this.ready;
    this.items.set([]);
    await Preferences.remove({ key: this.storageKey });
  }

  private async save(): Promise<void> {
    await Preferences.set({
      key: this.storageKey,
      value: JSON.stringify(this.items()),
    });
  }
}