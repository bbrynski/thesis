import { TestBed, inject } from '@angular/core/testing';

import { DestinyService } from './destiny.service';

describe('DestinyService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [DestinyService]
    });
  });

  it('should be created', inject([DestinyService], (service: DestinyService) => {
    expect(service).toBeTruthy();
  }));
});
