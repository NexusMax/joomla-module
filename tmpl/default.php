<?php
defined('_JEXEC') or die ;
?>

<div class="container publishIco_container">
	<?php if(isset($_GET['done'])): ?>
		<div class="row">
			<div class="col-md-12 text-center">
				<h2>
					<?php if($_GET['done'] === 'created'): ?>
						Your ICO published
					<?php elseif($_GET['done'] === 'deleted'): ?>
						ICO deleted
					<?php elseif($_GET['done'] === 'update'): ?>publish
						ICO updated
					<?php elseif($_GET['done'] === 'publish'): ?>
						ICO published
					<?php endif; ?>
				</h2>
			</div>
		</div>
	<?php else: ?>
	<form action="" method="POST" enctype="multipart/form-data">

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
	    			<label class="publishIco_label" for="name">Ico name</label>
	    			<input type="text" class="form-control" name="name" id="name" value="<?= $model->name ?>" required>
	    		</div>
	    		<div class="form-group">
	    			<label for="site" class="publishIco_label">Website url</label>
					<input type="text" class="form-control" name="site" id="site" value="<?= $model->site ?>" required>
	    		</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="logo" class="publishIco_label">Logo</label>
					<label class="fileContainer">
            		<span class="logoDesc">Upload your logo</span>
						<input type="file" name="logo" id="logo" value="<?= $model->logo ?>">
					</label>
				</div>
			</div>
		</div>

		<div class="row">
			<p class="publishIco_label publishIco_label-p">Social media links</p>
			<div class="col-md-6">
				<?php $placeH = array('bitcointalk','reddit','bitcointalk','github','medium'); $qew=0;?>
				<?php for($i = 0; $i < count($socials)/2; $i++): ?>
				<div class="form-group">
	    			<input type="text" class="form-control" name="social[<?= $socials[$i]->form_name ?>]" id="<?= $socials[$i]->form_name ?>" placeholder="<?= $socials[$i]->name ?>" value="<?= $model->social[$socials[$i]->form_name] ?>">
	    		</div>
	    		<?php endfor; ?>
			</div>
			<div class="col-md-6">
				<?php for($i = count($socials) - 1; $i >= count($socials)/2; $i--): ?>
					<?php $qew++ ?>
				<div class="form-group">
	    			<input type="text" class="form-control" name="social[<?= $socials[$i]->form_name ?>]" id="<?= $socials[$i]->form_name ?>" placeholder="<?= $placeH[$qew]?>" value="<?= $model->social[$socials[$i]->form_name] ?>">
	    		</div>
	    		<?php endfor; ?>
			</div>
		</div>

	    <div class="row">
	    	<div class="col-md-12">
				<div class="form-group">
					<label class="publishIco_label" for="category_id">Categories</label>
					
	    			<select class="ui fluid dropdown" name="category_id[]" id="category_id" required multiple="">
	    				<option value="" disabled="disabled" selected="selected">Choose category</option>
			    		<?php for($i = 0; $i < count($categories); $i++): ?>
			    		<option value="<?= $categories[$i]->id ?>" <?php if(is_array( $model->category_id) && in_array($categories[$i]->id, $model->category_id)): ?> selected <?php endif; ?>><?= $categories[$i]->name ?></option>
		    			<?php endfor; ?>
	    			</select>
	    		</div>
	    	</div>
	    </div>

	    <div class="row">
	  		<div class="col-md-3">
	  			<div class="form-group">
	    			<label class="publishIco_label" for="ico_start">Ico start</label>
				  <div class="ui calendar" id="example1">
				    <div class="ui input left icon">
				      <i class="calendar icon"></i>
				      <input type="text" placeholder="Date/Time"  name="ico_start" id="ico_start" value="<?= $model->ico_start ?>" required>
				    </div>
				  </div>
	    		</div>
	  		</div>
	  		<div class="col-md-3">
	  			<div class="form-group">
	    			<label class="publishIco_label" for="ico_end">Ico end</label>
					<div class="ui calendar" id="example2">
					  <div class="ui input left icon">
					    <i class="calendar icon"></i>
					    <input type="text" placeholder="Date/Time" name="ico_end" id="ico_end" value="<?= $model->ico_end ?>" required>
					  </div>
					</div>
	    		</div>
	  		</div>
	  		<div class="col-md-6">
	  			<div class="form-group">
	    			<label class="publishIco_label" for="token">Token</label>
					<input type="text" class="form-control" name="token" id="token" value="<?= $model->token ?>" required>
	    		</div>
	  		</div>
	    </div>

	    <div class="row">
	  		<div class="col-md-6">
	  			<div class="form-group">
	    			<label class="publishIco_label" for="price">Price</label>
					<input type="number" class="form-control" name="price" id="price" value="<?= $model->price ?>" required>
	    		</div>
	  		</div>
	  		<div class="col-md-6">
	  			<div class="form-group">
	    			<label class="publishIco_label" for="min_investment">Min. investment</label>
					<input type="number" class="form-control" name="min_investment" id="min_investment" value="<?= $model->min_investment ?>" required>
	    		</div>
	  		</div>
	    </div>

	    <div class="row">
	  		<div class="col-md-6">
	  			<div class="form-group">
	    			<label class="publishIco_label" for="pre_ico_price">Pre-ico Price</label>
					<input type="number" class="form-control" name="pre_ico_price" id="pre_ico_price" value="<?= $model->pre_ico_price ?>" required>
	    		</div>
	  		</div>
	  		<div class="col-md-6">
	  			<div class="form-group">
	    			<label class="publishIco_label" for="soft_cap">Soft cap</label>
					<input type="number" class="form-control" name="soft_cap" id="soft_cap" value="<?= $model->soft_cap ?>" required>
	    		</div>
	  		</div>
	    </div>

	    <div class="row">
	  		<div class="col-md-6">
	  			<div class="form-group">
	    			<label class="publishIco_label" for="total_suply">Total suply</label>
					<input type="number" class="form-control" name="total_suply" id="total_suply" value="<?= $model->total_suply ?>" required>
	    		</div>
	  		</div>
	  		<div class="col-md-6">
	  			<div class="form-group">
	    			<label class="publishIco_label" for="hard_cap">Hard cap</label>
					<input type="number" class="form-control" name="hard_cap" id="hard_cap" value="<?= $model->hard_cap ?>" required>
	    		</div>
	  		</div>
	    </div>

	    <div class="row">
	  		<div class="col-md-6">
	  			<div class="form-group">
	    			<label class="publishIco_label" for="country">Country</label>
					<input type="text" class="form-control" name="country" id="country" value="<?= $model->country ?>" required>
	    		</div>
	  		</div>
	  		<div class="col-md-6">
	  			<div class="form-group">
	    			<label class="publishIco_label" for="restricted_area">Restricted area</label>
					<input type="text" class="form-control" name="restricted_area" id="restricted_area" value="<?= $model->restricted_area ?>" required>
	    		</div>
	  		</div>
	    </div>

	    <div class="row">
	  		<div class="col-md-12">
	  			<div class="form-inline">
	    			<label class="publishIco_label" for="bonus">Bonus</label>
					<input type="checkbox" class="form-control" name="bonus" id="bonus" <?php if($model->bonus): ?> checked <?php endif;?>>
	  
	    			<label class="publishIco_label" for="bounty">Bounty</label>
					<input type="checkbox" class="form-control" name="bounty" id="bounty" <?php if($model->bounty): ?> checked <?php endif;?>>
	    		
	    			<label class="publishIco_label" for="whitelist_kyc">Whitelist/KYC</label>
					<input type="checkbox" class="form-control" name="whitelist_kyc" id="whitelist_kyc" <?php if($model->whitelist_kyc): ?> checked <?php endif;?>>

					<?php if(isset($model->user->groups[8]) && $model->user->groups[8] === '8'): ?>
					<label class="publishIco_label" for="active">Active</label>
					<input type="checkbox" class="form-control" name="active" id="active" <?php if($model->active): ?> checked <?php endif;?>>

					<label class="publishIco_label" for="premium">Premium</label>
					<input type="checkbox" class="form-control" name="premium" id="premium" <?php if($model->premium): ?> checked <?php endif;?>>

					<input type="number" class="form-control" name="star" id="star" value="<?= $model->star ?>" step="0.01" placeholder="Star">
					<?php endif; ?>
	    		</div>
	  		</div>
	    </div>
		
		<div class="row">
			<div class="col-md-12">
	  			<div class="form-group">
	    			<label class="publishIco_label" for="about">About</label>
					<textarea name="about" id="" cols="30" rows="10" required><?= $model->about ?></textarea>
	    		</div>
	  		</div>
		</div>

		<div class="row">
			<div class="col-md-12">
	  			<div class="form-group">
	    			<label class="publishIco_label" for="video">Video url</label>
	    			<input type="text" class="form-control" name="video" id="video" value="<?= $model->video ?>">
	    		</div>
	  		</div>
		</div>
		
		<div class="teams">
		<?php $i = 0 ; ?>
		<?php foreach ($model->team as $key): ?>
			<?php if($model->team[0]['name'] === null || $i === count($model->team) - 1): ?>
				<?php 
				$flag = '+';
				$class = 'plus';
				?>
			<?php else: ?>
				<?php 
				$flag = '-';
				$class = 'minus';
				?>
			<?php endif; ?>
		<div class="row">
			<div class="col-md-6">
	  			<div class="form-group">
	    			<label class="publishIco_label" for="team">Team</label>
	    			<div class="row">
	    				<div class="col-md-6">
	    					<div class="form-group">
	    						<input type="text" class="form-control nm" name="team[<?= $i ?>][name]" id="team[name]" placeholder="Name" value="<?= $key['name'] ?>" required>
	    					</div>
		    			</div>
		    			<div class="col-md-6">
		    				<div class="form-group">
		    					<input type="text" class="form-control tt" name="team[<?= $i ?>][title]" id="team[title]" placeholder="Title" value="<?= $key['title'] ?>" required>
		    				</div>
		    			</div>
	    			</div>
	    			<div class="row">
	    				<div class="col-md-12">
	    					<div class="form-group">
	    						<input type="text" class="form-control lk" name="team[<?= $i ?>][linkedin]" id="team[linkedin]" placeholder="LinkedIN URL" value="<?= $key['linkedin'] ?>">
	    					</div>
	    				</div>
	    			</div>
	    		</div>
	    		<div class="form-inline teamCheckbox-inline">
	    			<span class="<?= $class ?> plus-team" data-number="<?= $i ?>"><?= $flag ?></span>
	    			<label class="publishIco_label">Advisor
						<input type="checkbox" class="form-control ad" name="team[<?= $i ?>][advisor]" <?php if($key['advisor']): ?> checked <?php endif;?>>
					</label>
	  
	    			<label class="publishIco_label">Team member
						<input type="checkbox" class="form-control mm" name="team[<?= $i ?>][member]" <?php if($key['member']): ?> checked <?php endif;?>>
					</label>
	    		</div>
	  		</div>
	  		<div class="col-md-6">
          <div class="form-group">
            <label for="logo" class="publishIco_label">Photo</label>
            <label class="fileContainer">
              <span class="logoDesc">Upload your photo</span>
              <input type="file" name="team[<?= $i ?>][photo]" id="team_photo" value="<?= $key['photo'] ?>">
            </label>
          </div>
	  		</div>
		</div>
		<?php $i++; ?>
		<?php endforeach; ?>
		</div>
	
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label class="publishIco_label" for="white_paper">White paper url</label>
					<input type="text" class="form-control" name="white_paper" id="white_paper" value="<?= $model->white_paper ?>" required>
				</div>
			</div>
		</div>
		<div class="road-map">
		<?php $i = 0 ; ?>
		<?php foreach ($model->road_map as $key): ?>

			<?php if($model->road_map[0]['desc'] === null || $i === count($model->road_map) - 1): ?>
				<?php 
				$flag = '+';
				$class = 'plus';
				?>
			<?php else: ?>
				<?php 
				$flag = '-';
				$class = 'minus road';
				?>
			<?php endif; ?>

		<div class="row">
			<p class="publishIco_label publishIco_label-p">Road Map</p>
			<div class="col-md-6">
				<div class="form-group">
					<div class="ui calendar" id="example3">
					  <div class="ui input left icon">
					    <i class="calendar icon"></i>
					    <input type="text" name="road_map[<?= $i ?>][created_at]" class="dt" value="<?= $key['created_at'] ?>" placeholder="Date/Time" required>
					  </div>
					</div>
	    		</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<input type="text" class="form-control mp" name="road_map[<?= $i ?>][desc]" value="<?= $key['desc'] ?>" placeholder="Description" required>
	    		</div>
			</div>
			<div class="col-md-12">
				<span class="<?= $class ?> plus-road" data-number="<?= $i ?>"><?= $flag ?></span>
			</div>
			
		</div>
		<?php $i++; ?>
		<?php endforeach; ?>
		</div>
		<div class="text-center">
			<button type="submit" class="btn btn_addIco">Add ico</button>		
		</div>
	</form>
	<?php endif; ?>
</div>