import { TestBed, inject } from '@angular/core/testing';

import { PrincipleService } from './principle.service';

describe('PrincipleService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [PrincipleService]
    });
  });

  it('should be created', inject([PrincipleService], (service: PrincipleService) => {
    expect(service).toBeTruthy();
  }));
});
