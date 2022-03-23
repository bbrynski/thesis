import { TestBed, inject } from '@angular/core/testing';

import { BootsService } from './boots.service';

describe('BootsService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [BootsService]
    });
  });

  it('should be created', inject([BootsService], (service: BootsService) => {
    expect(service).toBeTruthy();
  }));
});
