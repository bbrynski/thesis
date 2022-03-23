import { TestBed, inject } from '@angular/core/testing';

import { BootsCategoryService } from './boots-category.service';

describe('BootsCategoryService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [BootsCategoryService]
    });
  });

  it('should be created', inject([BootsCategoryService], (service: BootsCategoryService) => {
    expect(service).toBeTruthy();
  }));
});
