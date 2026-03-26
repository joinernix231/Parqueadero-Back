# Manual testing checklist — parking system

## Entry flow

- [ ] Find an existing vehicle by license plate  
- [ ] Register a new vehicle when none exists  
- [ ] Select parking lot and available spot  
- [ ] Complete entry successfully  
- [ ] Confirm PDF download (if applicable)  
- [ ] Check PDF content (correct data)  
- [ ] Confirm ticket details in the UI/API response  
- [ ] Re-download receipt if the feature exists  
- [ ] Confirm spot shows as occupied  

## Exit flow

- [ ] Find active ticket by plate  
- [ ] Confirm ticket details  
- [ ] Confirm live price calculation  
- [ ] Confirm elapsed time updates  
- [ ] Optional: generate pre-exit PDF  
- [ ] Confirm exit  
- [ ] Confirm final summary  
- [ ] Generate exit PDF with final amount  
- [ ] Verify exit PDF totals  
- [ ] Confirm spot is freed  
- [ ] Confirm ticket is closed  

## Edge cases

- [ ] Entry with an occupied spot (expect error)  
- [ ] Exit with invalid ticket  
- [ ] Exit PDF without exit time recorded  
- [ ] Vehicle types: car, motorcycle, truck  
- [ ] Day vs night pricing windows  
- [ ] Multiple parking lots  
- [ ] Auth required on protected routes  

## PDF checks

- [ ] Layout is readable  
- [ ] All required fields present  
- [ ] Date/time formatting  
- [ ] Price math  
- [ ] Opens in major browsers  
- [ ] Prints cleanly  

## Test notes

### Browsers

- Chrome (current)  
- Firefox (current)  
- Edge (current)  

### Pricing scenarios

- Day rates only  
- Night rates only  
- Spanning day → night  
- Spanning midnight  

### Data to verify on receipts

- Vehicle (plate, owner, type)  
- Lot (name, address)  
- Spot (number, type)  
- Entry/exit times  
- Hours and totals  
- Payment method when paid  
